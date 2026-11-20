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

        if ($attendance->jam_istirahat_mulai && ! $attendance->jam_istirahat_selesai) {
            return back()->with('error', 'Anda masih dalam status istirahat. Silakan absen selesai istirahat terlebih dahulu.');
        }

        $attendance->update([
            'jam_keluar' => now()->format('H:i:s'),
        ]);

        return back()->with('success', 'Absen keluar berhasil dicatat.');
    }

    public function breakStart(Request $request)
    {
        $attendance = $request->user()->attendances()
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        if (! $attendance) {
            return back()->with('error', 'Anda belum melakukan absen masuk hari ini.');
        }

        if ($attendance->jam_keluar) {
            return back()->with('error', 'Anda sudah melakukan absen keluar hari ini.');
        }

        if ($attendance->jam_istirahat_mulai) {
            return back()->with('error', 'Anda sudah mencatat mulai istirahat hari ini.');
        }

        $attendance->update([
            'jam_istirahat_mulai' => now()->format('H:i:s'),
        ]);

        return back()->with('success', 'Absen mulai istirahat berhasil dicatat. Selamat beristirahat!');
    }

    public function breakEnd(Request $request)
    {
        $attendance = $request->user()->attendances()
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        if (! $attendance || ! $attendance->jam_istirahat_mulai) {
            return back()->with('error', 'Anda belum melakukan absen mulai istirahat.');
        }

        if ($attendance->jam_keluar) {
            return back()->with('error', 'Anda sudah melakukan absen keluar hari ini.');
        }

        if ($attendance->jam_istirahat_selesai) {
            return back()->with('error', 'Anda sudah mencatat selesai istirahat hari ini.');
        }

        // Jam istirahat maksimal jam 13:00 (jam 1 siang), lewat dari itu dianggap telat
        $isLate = now()->format('H:i') > '13:00';
        $statusIstirahat = $isLate ? 'telat' : 'tepat_waktu';

        $updates = [
            'jam_istirahat_selesai' => now()->format('H:i:s'),
            'status_istirahat' => $statusIstirahat,
        ];

        // Jika lewat dari jam 13:00, status kehadiran berubah menjadi telat
        if ($isLate) {
            $updates['status'] = 'telat';
        }

        $attendance->update($updates);

        if ($isLate) {
            return back()->with('error', 'Absen kembali istirahat berhasil dicatat, namun Anda tercatat terlambat (melewati batas pukul 13:00).');
        }

        return back()->with('success', 'Absen selesai istirahat berhasil dicatat tepat waktu. Selamat melanjutkan pekerjaan!');
    }
}
