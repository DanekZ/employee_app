<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LeaveRequestController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function(){
    // Karyawan
    Route::resource('izin', LeaveRequestController::class)
        ->only(['index', 'create', 'store'])
        ->parameters(['izin' => 'leaveRequest']);

    Route::middleware(['role:atasan'])->group(function (){
        Route::patch('/izin/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])->name("izin.approve");
        Route::patch('/izin/{LeaveRequest}/reject', [LeaveRequestController::class, 'reject'])->name('izin.reject');
    });
});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
