<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OfficeTripController extends Controller
{
    public function index(Request $request)
    {
        $officeTrips = $request->user()->officeTrips()->latest()->get();

        return view('dinas.index', [
            'officeTrips' => $officeTrips,
        ]);
    }

    public function create()
    {
        return view('dinas.create');
    }

    public function store(Request $request)
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
}
