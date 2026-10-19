<?php

namespace App\Http\Controllers;

use App\Models\OfficeTrip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfficeTripController extends Controller
{
    public function index(Request $request): View
    {
        $officeTrips = $request->user()->officeTrips()->latest()->get();

        return view('dinas.index', [
            'officeTrips' => $officeTrips,
        ]);
    }

    public function create(): View
    {
        return view('dinas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tanggal' => 'required|date',
            'tujuan_alamat' => 'required|string|max:255',
            'jam_keluar' => 'required|date_format:H:i',
            'jam_kembali' => 'required|date_format:H:i|after:jam_keluar',
            'alat_transportasi' => 'required|in:kendaraan_dinas,kendaraan_pribadi,transportasi_umum',
            'alasan' => 'required|string',
        ], [
            'tanggal.required' => 'Tanggal dinas wajib dipilih.',
            'tujuan_alamat.required' => 'Tujuan/alamat wajib diisi.',
            'jam_keluar.required' => 'Jam keluar wajib diisi.',
            'jam_kembali.required' => 'Jam kembali wajib diisi.',
            'jam_kembali.after' => 'Jam kembali harus setelah jam keluar.',
            'alat_transportasi.required' => 'Alat transportasi wajib dipilih.',
            'alasan.required' => 'Alasan dinas wajib diisi.',
        ]);

        $request->user()->officeTrips()->create($request->only([
            'tanggal', 'tujuan_alamat', 'jam_keluar', 'jam_kembali', 'alat_transportasi', 'alasan',
        ]));

        return redirect()->route('dinas.index')->with('success', 'Pengajuan dinas berhasil dikirim.');
    }

    public function approve(OfficeTrip $officeTrip): RedirectResponse
    {
        $officeTrip->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Pengajuan dinas disetujui.');
    }

    public function reject(OfficeTrip $officeTrip): RedirectResponse
    {
        $officeTrip->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Pengajuan dinas ditolak.');
    }
}
