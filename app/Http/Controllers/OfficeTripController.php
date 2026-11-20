<?php

namespace App\Http\Controllers;

use App\Models\OfficeTrip;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfficeTripController extends Controller
{
    public function index(Request $request): View
    {
        $officeTrips = $request->user()->officeTrips()
            ->with('menumpangDengan')
            ->latest()
            ->get();

        return view('dinas.index', [
            'officeTrips' => $officeTrips,
        ]);
    }

    public function create(): View
    {
        $rekanKerja = User::where('id', '!=', auth()->id())
            ->whereIn('role', ['karyawan', 'admin'])
            ->orderBy('name')
            ->get();

        return view('dinas.create', [
            'rekanKerja' => $rekanKerja,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'tujuan_alamat' => 'required|string|max:255',
            'jam_keluar' => 'required|date_format:H:i',
            'jam_kembali' => 'required|date_format:H:i|after:jam_keluar',
            'alat_transportasi' => 'required|in:kendaraan_dinas,kendaraan_pribadi,transportasi_umum',
            'peran_kendaraan' => 'nullable|in:bawa_sendiri,menumpang',
            'menumpang_user_id' => 'nullable|required_if:peran_kendaraan,menumpang|exists:users,id',
            'alasan' => 'required|string',
        ], [
            'tanggal.required' => 'Tanggal dinas wajib dipilih.',
            'tujuan_alamat.required' => 'Tujuan/alamat wajib diisi.',
            'jam_keluar.required' => 'Jam keluar wajib diisi.',
            'jam_kembali.required' => 'Jam kembali wajib diisi.',
            'jam_kembali.after' => 'Jam kembali harus setelah jam keluar.',
            'alat_transportasi.required' => 'Alat transportasi wajib dipilih.',
            'menumpang_user_id.required_if' => 'Silakan pilih rekan kerja yang Anda tumpangi.',
            'alasan.required' => 'Alasan dinas wajib diisi.',
        ]);

        if ($validated['alat_transportasi'] === 'kendaraan_pribadi') {
            $validated['peran_kendaraan'] = $validated['peran_kendaraan'] ?? 'bawa_sendiri';
            if ($validated['peran_kendaraan'] === 'bawa_sendiri') {
                $validated['menumpang_user_id'] = null;
            }
        } else {
            $validated['peran_kendaraan'] = null;
            $validated['menumpang_user_id'] = null;
        }

        $request->user()->officeTrips()->create($validated);

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
