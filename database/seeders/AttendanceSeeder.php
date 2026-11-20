<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data karyawan sesuai data yang ada di UserSeeder
        $employees = User::whereIn('email', [
            'novi@sinarta.test',
            'sela@sinarta.test',
            'denissa@sinarta.test',
            'anoriza@sinarta.test',
            'dane@sinarta.test',
            'admin@sinarta.test',
            'rasyadah@sinarta.test',
        ])->get();

        // Fallback jika belum di-seed menggunakan email di atas
        if ($employees->isEmpty()) {
            $employees = User::all();
        }

        if ($employees->isEmpty()) {
            $this->command?->warn('Tidak ada data karyawan ditemukan. Silakan jalankan UserSeeder terlebih dahulu.');

            return;
        }

        // Rentang waktu: dari awal bulan lalu hingga hari ini (mencakup preset Bulan Lalu & Bulan Ini)
        $startDate = now()->subMonth()->startOfMonth();
        $endDate = now();
        $period = CarbonPeriod::create($startDate, $endDate);

        // Profil kedisiplinan per karyawan agar data bervariasi dan realistis untuk visualisasi/laporan
        $profiles = [
            'novi@sinarta.test' => [
                'late_chance' => 3,  // Sangat Disiplin
                'alpha_chance' => 0,
            ],
            'sela@sinarta.test' => [
                'late_chance' => 5,  // Sangat Disiplin (~95%)
                'alpha_chance' => 0,
            ],
            'denissa@sinarta.test' => [
                'late_chance' => 6,  // Sangat Disiplin (~94%)
                'alpha_chance' => 0,
            ],
            'anoriza@sinarta.test' => [
                'late_chance' => 15, // Cukup Disiplin (~85%)
                'alpha_chance' => 2,
            ],
            'dane@sinarta.test' => [
                'late_chance' => 28, // Perlu Evaluasi (~72%)
                'alpha_chance' => 5,
            ],
            'admin@sinarta.test' => [
                'late_chance' => 8,  // Cukup Disiplin (~92%)
                'alpha_chance' => 0,
            ],
            'rasyadah@sinarta.test' => [
                'late_chance' => 4,  // Sangat Disiplin (~96%)
                'alpha_chance' => 0,
            ],
        ];

        // Koordinat kantor (Sinarta Office di Jakarta)
        $baseLat = -6.2088000;
        $baseLng = 106.8456000;

        foreach ($employees as $employee) {
            $profile = $profiles[$employee->email] ?? ['late_chance' => 10, 'alpha_chance' => 0];

            foreach ($period as $date) {
                // Lewati akhir pekan (Sabtu & Minggu)
                if ($date->isWeekend()) {
                    continue;
                }

                $isToday = $date->isToday();

                // Simulasi alpha / tidak hadir
                if (! $isToday && mt_rand(1, 100) <= $profile['alpha_chance']) {
                    Attendance::updateOrCreate(
                        [
                            'user_id' => $employee->id,
                            'tanggal' => $date->toDateString(),
                        ],
                        [
                            'jam_masuk' => null,
                            'jam_keluar' => null,
                            'latitude' => null,
                            'longitude' => null,
                            'status' => 'alpha',
                        ]
                    );

                    continue;
                }

                // Tentukan status ketepatan waktu (> 08:00 telat)
                $isLate = mt_rand(1, 100) <= $profile['late_chance'];

                if ($isLate) {
                    // Masuk antara 08:05 sampai 08:35
                    $minute = str_pad((string) mt_rand(5, 35), 2, '0', STR_PAD_LEFT);
                    $second = str_pad((string) mt_rand(0, 59), 2, '0', STR_PAD_LEFT);
                    $jamMasuk = "08:{$minute}:{$second}";
                    $status = 'telat';
                } else {
                    // Masuk antara 07:35 sampai 07:58
                    $minute = str_pad((string) mt_rand(35, 58), 2, '0', STR_PAD_LEFT);
                    $second = str_pad((string) mt_rand(0, 59), 2, '0', STR_PAD_LEFT);
                    $jamMasuk = "07:{$minute}:{$second}";
                    $status = 'hadir';
                }

                // Waktu Istirahat (Jadwal istirahat 12:00 - 13:00)
                $jamIstirahatMulai = null;
                $jamIstirahatSelesai = null;
                $statusIstirahat = null;

                if (! $isToday || now()->hour >= 12) {
                    $istMinute = str_pad((string) mt_rand(0, 5), 2, '0', STR_PAD_LEFT);
                    $istSecond = str_pad((string) mt_rand(0, 59), 2, '0', STR_PAD_LEFT);
                    $jamIstirahatMulai = "12:{$istMinute}:{$istSecond}";

                    // Kemungkinan telat kembali dari istirahat (> 13:00)
                    $isLateBreak = mt_rand(1, 100) <= ($profile['late_chance'] ?? 10);

                    if (! $isToday || now()->hour >= 13) {
                        if ($isLateBreak) {
                            // Kembali antara 13:03 sampai 13:20 (Telat)
                            $backMinute = str_pad((string) mt_rand(3, 20), 2, '0', STR_PAD_LEFT);
                            $backSecond = str_pad((string) mt_rand(0, 59), 2, '0', STR_PAD_LEFT);
                            $jamIstirahatSelesai = "13:{$backMinute}:{$backSecond}";
                            $statusIstirahat = 'telat';
                            $status = 'telat'; // Jika telat istirahat, status presensi menjadi telat
                        } else {
                            // Kembali antara 12:45 sampai 12:58 (Tepat Waktu)
                            $backMinute = str_pad((string) mt_rand(45, 58), 2, '0', STR_PAD_LEFT);
                            $backSecond = str_pad((string) mt_rand(0, 59), 2, '0', STR_PAD_LEFT);
                            $jamIstirahatSelesai = "12:{$backMinute}:{$backSecond}";
                            $statusIstirahat = 'tepat_waktu';
                        }
                    }
                }

                // Jam keluar
                if ($isToday) {
                    // Jika hari ini: sebagian karyawan masih bekerja (belum checkout), sebagian sudah checkout
                    $hasCheckedOut = mt_rand(1, 100) <= 25;
                    if ($hasCheckedOut) {
                        $jamKeluar = '16:'.str_pad((string) mt_rand(30, 59), 2, '0', STR_PAD_LEFT).':00';
                    } else {
                        $jamKeluar = null;
                    }
                } else {
                    // Hari lampau pulang antara 17:00 sampai 17:45
                    $hour = mt_rand(17, 18);
                    $minute = $hour === 18
                        ? str_pad((string) mt_rand(0, 15), 2, '0', STR_PAD_LEFT)
                        : str_pad((string) mt_rand(0, 45), 2, '0', STR_PAD_LEFT);
                    $second = str_pad((string) mt_rand(0, 59), 2, '0', STR_PAD_LEFT);
                    $jamKeluar = "{$hour}:{$minute}:{$second}";
                }

                // Variasi koordinat di sekitar kantor
                $latOffset = mt_rand(-30, 30) / 100000;
                $lngOffset = mt_rand(-30, 30) / 100000;

                Attendance::updateOrCreate(
                    [
                        'user_id' => $employee->id,
                        'tanggal' => $date->toDateString(),
                    ],
                    [
                        'jam_masuk' => $jamMasuk,
                        'jam_istirahat_mulai' => $jamIstirahatMulai,
                        'jam_istirahat_selesai' => $jamIstirahatSelesai,
                        'jam_keluar' => $jamKeluar,
                        'latitude' => round($baseLat + $latOffset, 7),
                        'longitude' => round($baseLng + $lngOffset, 7),
                        'status' => $status,
                        'status_istirahat' => $statusIstirahat,
                    ]
                );
            }
        }
    }
}
