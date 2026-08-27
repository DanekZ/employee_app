<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\OfficeTrip;
use App\Models\OvertimeRequest;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $bawahanIds = $request->user()->bawahan()->pluck('id');

        $izin = LeaveRequest::whereIn('user_id', $bawahanIds)
            ->where('status', 'pending')->with('user')->latest()->get();

        $lembur = OvertimeRequest::whereIn('user_id', $bawahanIds)
            ->where('status', 'pending')->with('user')->latest()->get();

        $dinas = OfficeTrip::whereIn('user_id', $bawahanIds)
            ->where('status', 'pending')->with('user')->latest()->get();

        return view('approval.index', [
            'izin' => $izin,
            'lembur' => $lembur,
            'dinas' => $dinas,
        ]);
    }

    public function absensi(Request $request)
    {
        $query = Attendance::with('user')->orderByDesc('tanggal')->orderByDesc('jam_masuk');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        } elseif ($request->filled('bulan')) {
            $bulan = Carbon::parse($request->bulan);
            $query->whereYear('tanggal', $bulan->year)->whereMonth('tanggal', $bulan->month);
        } else {
            $query->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->paginate(20)->withQueryString();
        $karyawanList = User::where('role', 'karyawan')->orderBy('name')->get();

        return view('approval.absensi', [
            'attendances' => $attendances,
            'karyawanList' => $karyawanList,
        ]);
    }

    public function laporan(Request $request)
    {
        $startDate = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $endDate = $request->input('tanggal_selesai', now()->toDateString());
        $userId = $request->input('user_id');

        $query = Attendance::with('user')
            ->whereBetween('tanggal', [$startDate, $endDate]);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $attendances = (clone $query)->orderBy('tanggal')->get();

        $totalCount = $attendances->count();
        $hadirCount = $attendances->where('status', 'hadir')->count();
        $telatCount = $attendances->where('status', 'telat')->count();
        $punctualityRate = $totalCount > 0 ? round(($hadirCount / $totalCount) * 100, 1) : 0;

        $dailyLabels = [];
        $dailyHadir = [];
        $dailyTelat = [];

        $period = CarbonPeriod::create($startDate, $endDate);
        $groupedByDate = $attendances->groupBy('tanggal');

        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $dailyLabels[] = $date->translatedFormat('d M');

            $dayRecords = $groupedByDate->get($formattedDate, collect());
            $dailyHadir[] = $dayRecords->where('status', 'hadir')->count();
            $dailyTelat[] = $dayRecords->where('status', 'telat')->count();
        }

        $karyawanQuery = User::where('role', 'karyawan');
        if ($userId) {
            $karyawanQuery->where('id', $userId);
        }
        $karyawanList = User::where('role', 'karyawan')->orderBy('name')->get();
        $targetKaryawan = $karyawanQuery->orderBy('name')->get();

        $employeeStats = [];
        foreach ($targetKaryawan as $emp) {
            $empAttendances = $attendances->where('user_id', $emp->id);
            $empTotal = $empAttendances->count();
            $empHadir = $empAttendances->where('status', 'hadir')->count();
            $empTelat = $empAttendances->where('status', 'telat')->count();
            $empRate = $empTotal > 0 ? round(($empHadir / $empTotal) * 100, 1) : 0;

            $rating = 'Belum Ada Data';
            $badgeClass = 'bg-gray-100 text-gray-700';

            if ($empTotal > 0) {
                if ($empRate >= 95) {
                    $rating = 'Sangat Disiplin';
                    $badgeClass = 'bg-emerald-100 text-emerald-800 border border-emerald-300';
                } elseif ($empRate >= 80) {
                    $rating = 'Cukup Disiplin';
                    $badgeClass = 'bg-blue-100 text-blue-800 border border-blue-300';
                } else {
                    $rating = 'Perlu Evaluasi';
                    $badgeClass = 'bg-rose-100 text-rose-800 border border-rose-300';
                }
            }

            $employeeStats[] = [
                'user' => $emp,
                'total' => $empTotal,
                'hadir' => $empHadir,
                'telat' => $empTelat,
                'rate' => $empRate,
                'rating' => $rating,
                'badge_class' => $badgeClass,
            ];
        }

        return view('approval.laporan', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedUserId' => $userId,
            'karyawanList' => $karyawanList,
            'totalCount' => $totalCount,
            'hadirCount' => $hadirCount,
            'telatCount' => $telatCount,
            'punctualityRate' => $punctualityRate,
            'dailyLabels' => $dailyLabels,
            'dailyHadir' => $dailyHadir,
            'dailyTelat' => $dailyTelat,
            'employeeStats' => $employeeStats,
        ]);
    }

    public function rekap(Request $request)
    {
        $userId = $request->input('user_id');
        $status = $request->input('status');
        $bulan = $request->input('bulan');

        $userRole = $request->user()->role;
        $bawahanIds = $userRole === 'atasan' ? $request->user()->bawahan()->pluck('id') : null;

        $applyFilters = function ($query) use ($userId, $status, $bulan, $userRole, $bawahanIds) {
            if ($userRole === 'atasan' && $bawahanIds !== null) {
                $query->whereIn('user_id', $bawahanIds);
            }
            if ($userId) {
                $query->where('user_id', $userId);
            }
            if ($status) {
                $query->where('status', $status);
            }
            if ($bulan) {
                $parsed = Carbon::parse($bulan);
                $query->whereYear('created_at', $parsed->year)
                    ->whereMonth('created_at', $parsed->month);
            }

            return $query;
        };

        $izinQuery = LeaveRequest::with('user');
        $applyFilters($izinQuery);
        $izin = (clone $izinQuery)->latest()->paginate(15, ['*'], 'izin_page')->withQueryString();

        $lemburQuery = OvertimeRequest::with('user');
        $applyFilters($lemburQuery);
        $lembur = (clone $lemburQuery)->latest()->paginate(15, ['*'], 'lembur_page')->withQueryString();

        $dinasQuery = OfficeTrip::with('user');
        $applyFilters($dinasQuery);
        $dinas = (clone $dinasQuery)->latest()->paginate(15, ['*'], 'dinas_page')->withQueryString();

        $totalIzinApproved = (clone $izinQuery)->where('status', 'approved')->count();
        $totalIzinPending = (clone $izinQuery)->where('status', 'pending')->count();
        $totalIzinRejected = (clone $izinQuery)->where('status', 'rejected')->count();

        $totalLemburApproved = (clone $lemburQuery)->where('status', 'approved')->count();
        $totalLemburPending = (clone $lemburQuery)->where('status', 'pending')->count();
        $totalLemburRejected = (clone $lemburQuery)->where('status', 'rejected')->count();

        $totalDinasApproved = (clone $dinasQuery)->where('status', 'approved')->count();
        $totalDinasPending = (clone $dinasQuery)->where('status', 'pending')->count();
        $totalDinasRejected = (clone $dinasQuery)->where('status', 'rejected')->count();

        $karyawanList = User::whereIn('role', ['karyawan', 'admin'])->orderBy('name')->get();

        return view('approval.rekap', [
            'izin' => $izin,
            'lembur' => $lembur,
            'dinas' => $dinas,
            'karyawanList' => $karyawanList,
            'selectedUserId' => $userId,
            'selectedStatus' => $status,
            'selectedBulan' => $bulan,
            'totalIzinApproved' => $totalIzinApproved,
            'totalIzinPending' => $totalIzinPending,
            'totalIzinRejected' => $totalIzinRejected,
            'totalLemburApproved' => $totalLemburApproved,
            'totalLemburPending' => $totalLemburPending,
            'totalLemburRejected' => $totalLemburRejected,
            'totalDinasApproved' => $totalDinasApproved,
            'totalDinasPending' => $totalDinasPending,
            'totalDinasRejected' => $totalDinasRejected,
        ]);
    }
}
