<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\LeaveRequest;


class LeaveRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $leaveRequest = $request->user()->leaveRequests()->latest()->get();

        return Inertia::render('leave/index', [
            'leaveRequests' => $leaveRequest,
        ]); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('leave/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:tidak_masuk,terlambat,pulang_awal,keluar_kantor',
            'tujuan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'durasi_menit' => 'nullable|integer|min:1',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i|after:jam_mulai',
            'keterangan' => 'required|string',
        ], [
            'tujuan.required' => 'Tujuan izin wajib diisi.',
            'tanggal.required' => 'Tanggal izin wajib dipilih.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            'keterangan.required' => 'Keterangan izin wajib diisi.',
        ] );

        $request->user()->leaveRequests()->create($request->only([
            'jenis', 'tujuan', 'tanggal_mulai', 'tanggal_selesai',
            'durasi_menit', 'jam_mulai', 'jam_selesai', 'keterangan',
        ]));

        return redirect()->route('izin.index')->with('success', 'pengajuan izin berhasil dikirim!');
    }

    public function approve(LeaveRequest $leaveRequest){
            $leaveRequest->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            return back()->with('success', 'Pengajuan izin disetujui.');
    }

    public function reject(LeaveRequest $leaveRequest){
        $leaveRequest->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Pengajuan izin ditolak.');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
