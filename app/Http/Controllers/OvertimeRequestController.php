<?php

namespace App\Http\Controllers;

use App\Models\OvertimeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OvertimeRequestController extends Controller
{
    public function index(Request $request): View
    {
        $overtimeRequests = $request->user()->overtimeRequests()->latest()->get();

        return view('lembur.index', [
            'overtimeRequests' => $overtimeRequests,
        ]);
    }

    public function create(): View
    {
        return view('lembur.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'lokasi_lembur' => 'required|string|max:255',
            'alasan' => 'required|string',
        ], [
            'tanggal.required' => 'Tanggal lembur wajib dipilih.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
            'lokasi_lembur.required' => 'Lokasi lembur wajib diisi.',
            'alasan.required' => 'Alasan lembur wajib diisi.',
        ]);

        $request->user()->overtimeRequests()->create($request->only([
            'tanggal', 'jam_mulai', 'jam_selesai', 'lokasi_lembur', 'alasan',
        ]));

        return redirect()->route('lembur.index')->with('success', 'Pengajuan lembur berhasil dikirim.');
    }

    public function approve(OvertimeRequest $overtimeRequest): RedirectResponse
    {
        $overtimeRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Pengajuan lembur disetujui.');
    }

    public function reject(OvertimeRequest $overtimeRequest): RedirectResponse
    {
        $overtimeRequest->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Pengajuan lembur ditolak.');
    }
}
