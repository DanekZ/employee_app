<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $today = $request->user()->attendances()
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        $riwayat = $request->user()->attendances()
            ->whereMonth('tanggal', now()->month)
            ->orderByDesc('tanggal')
            ->get();

        return view('attendance.index', [
            'today' => $today,
            'riwayat' => $riwayat,
        ]);
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $sudahAbsen = $request->user()->attendances()
            ->whereDate('tanggal', now()->toDateString())
            ->exists();

        if ($sudahAbsen) {
            return back()->with('error', 'Anda sudah melakukan absen masuk hari ini.');
        }

        // Asumsi: jam masuk kantor jam 08:00, lewat dari itu dianggap telat
        $status = now()->format('H:i') > '08:00' ? 'telat' : 'hadir';

        $request->user()->attendances()->create([
            'tanggal' => now()->toDateString(),
            'jam_masuk' => now()->format('H:i:s'),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => $status,
        ]);

        return back()->with('success', 'Absen masuk berhasil dicatat.');
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $attendance = $request->user()->attendances()
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        if (! $attendance) {
            return back()->with('error', 'Anda belum melakukan absen masuk hari ini.');
        }

        $attendance->update([
            'jam_keluar' => now()->format('H:i:s'),
        ]);

        return back()->with('success', 'Absen keluar berhasil dicatat.');
    }
}
