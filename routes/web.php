<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\OfficeTripController;
use App\Http\Controllers\OvertimeRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return redirect()->route(
        auth()->user()->role === 'atasan' ? 'approval.index' : 'absensi.index'
    );
})->name('home');

Route::middleware(['auth', 'role:karyawan'])->group(function () {
    // Karyawan izin
    Route::resource('izin', LeaveRequestController::class)
        ->only(['index', 'create', 'store'])
        ->parameters(['izin' => 'leaveRequest']);

    // Karyawan Lembur
    Route::resource('lembur', OvertimeRequestController::class)
        ->only('index', 'create', 'store')
        ->parameters(['lembur' => 'overtimeRequest']);

    // Karyawan Dinas luar
    Route::resource('dinas', OfficeTripController::class)
        ->only('index', 'create', 'store')
        ->parameters(['dinas' => 'officeTrip']);

    // absen
    Route::get('/absensi', [AttendanceController::class, 'index'])->name('absensi.index');
    Route::post('/absensi/check-in', [AttendanceController::class, 'checkIn'])->name('absensi.checkin');
    Route::post('/absensi/check-out', [AttendanceController::class, 'checkOut'])->name('absensi.checkout');
});

Route::middleware(['auth', 'role:atasan'])->group(function () {
    // Halaman gabungan
    Route::get('/approval', [ApprovalController::class, 'index'])->name('approval.index');

    // Approved/reject per modul
    Route::patch('/izin/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])->name('izin.approve');
    Route::patch('/izin/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])->name('izin.reject');

    Route::patch('/lembur/{overtimeRequest}/approve', [OvertimeRequestController::class, 'approve'])->name('lembur.approve');
    Route::patch('/lembur/{overtimeRequest}/reject', [OvertimeRequestController::class, 'reject'])->name('lembur.reject');

    Route::patch('/dinas/{officeTrip}/approve', [OfficeTripController::class, 'approve'])->name('dinas.approve');
    Route::patch('/dinas/{officeTrip}/reject', [OfficeTripController::class, 'reject'])->name('dinas.reject');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
