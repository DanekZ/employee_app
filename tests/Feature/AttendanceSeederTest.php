<?php

use App\Models\Attendance;
use App\Models\User;
use Database\Seeders\AttendanceSeeder;
use Database\Seeders\UserSeeder;

test('attendance seeder seeds attendances for all users in user seeder', function () {
    $this->seed(UserSeeder::class);
    $this->seed(AttendanceSeeder::class);

    $employees = User::whereIn('email', [
        'sela@sinarta.test',
        'denissa@sinarta.test',
        'anoriza@sinarta.test',
        'dane@sinarta.test',
        'admin@sinarta.test',
        'rasyadah@sinarta.test',
    ])->get();

    expect($employees)->toHaveCount(6);

    foreach ($employees as $employee) {
        $count = Attendance::where('user_id', $employee->id)->count();
        expect($count)->toBeGreaterThan(0);
    }
});

test('attendance seeder is idempotent and does not create duplicate entries for same user and date', function () {
    $this->seed(UserSeeder::class);
    $this->seed(AttendanceSeeder::class);
    $initialCount = Attendance::count();

    // Re-run AttendanceSeeder
    $this->seed(AttendanceSeeder::class);
    $afterCount = Attendance::count();

    expect($afterCount)->toBe($initialCount);
});

test('seeded attendances have valid status values and realistic times', function () {
    $this->seed(UserSeeder::class);
    $this->seed(AttendanceSeeder::class);

    $attendances = Attendance::all();
    expect($attendances->count())->toBeGreaterThan(0);

    foreach ($attendances as $attendance) {
        expect(['hadir', 'telat', 'alpha', 'izin'])->toContain($attendance->status);

        if ($attendance->status === 'hadir') {
            expect($attendance->jam_masuk)->not->toBeNull();
            expect($attendance->jam_masuk)->toBeLessThanOrEqual('08:00:00');
            if ($attendance->jam_istirahat_selesai) {
                expect($attendance->jam_istirahat_selesai)->toBeLessThanOrEqual('13:00:00');
            }
        } elseif ($attendance->status === 'telat') {
            expect($attendance->jam_masuk)->not->toBeNull();
            $isLateMorning = $attendance->jam_masuk > '08:00:00';
            $isLateBreak = $attendance->jam_istirahat_selesai && $attendance->jam_istirahat_selesai > '13:00:00';
            expect($isLateMorning || $isLateBreak)->toBeTrue();
        }
    }
});
