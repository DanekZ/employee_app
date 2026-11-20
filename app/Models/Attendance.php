<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal',
        'jam_masuk',
        'jam_istirahat_mulai',
        'jam_istirahat_selesai',
        'jam_keluar',
        'latitude',
        'longitude',
        'status',
        'status_istirahat',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Menghitung total durasi kerja berdasarkan jam masuk, istirahat, dan jam keluar.
     */
    public function getDurasiKerjaAttribute(): string
    {
        if (! $this->jam_masuk || ! $this->jam_keluar) {
            if ($this->jam_masuk) {
                if ($this->jam_istirahat_mulai && ! $this->jam_istirahat_selesai) {
                    return 'Sedang Istirahat';
                }

                return 'Sedang Bekerja';
            }

            return '-';
        }

        $masuk = Carbon::parse($this->jam_masuk);
        $keluar = Carbon::parse($this->jam_keluar);

        if ($keluar->lessThan($masuk)) {
            return '-';
        }

        $totalMinutes = $masuk->diffInMinutes($keluar);

        // Potong durasi istirahat jika tercatat
        if ($this->jam_istirahat_mulai && $this->jam_istirahat_selesai) {
            $istMulai = Carbon::parse($this->jam_istirahat_mulai);
            $istSelesai = Carbon::parse($this->jam_istirahat_selesai);

            if ($istSelesai->greaterThan($istMulai)) {
                $breakMinutes = $istMulai->diffInMinutes($istSelesai);
                $totalMinutes = max(0, $totalMinutes - $breakMinutes);
            }
        }

        $hours = intdiv($totalMinutes, 60);
        $minutes = $totalMinutes % 60;

        return "{$hours} jam {$minutes} menit";
    }

    /**
     * Memberikan keterangan detail mengenai ketepatan waktu kehadiran dan istirahat.
     */
    public function getKeteranganKetepatanAttribute(): string
    {
        $keterangan = [];

        // Cek ketepatan jam masuk kantor (standar 08:00)
        if ($this->jam_masuk) {
            $standardMasuk = Carbon::parse('08:00:00');
            $masuk = Carbon::parse($this->jam_masuk);

            if ($masuk->greaterThan($standardMasuk)) {
                $diffMinutes = $standardMasuk->diffInMinutes($masuk);
                $keterangan[] = $this->jam_istirahat_selesai
                    ? "Terlambat Masuk ({$diffMinutes} menit)"
                    : "Terlambat {$diffMinutes} menit";
            }
        }

        // Cek ketepatan kembali dari istirahat (standar 13:00)
        if ($this->jam_istirahat_selesai) {
            $standardIstirahat = Carbon::parse('13:00:00');
            $selesai = Carbon::parse($this->jam_istirahat_selesai);

            if ($selesai->greaterThan($standardIstirahat)) {
                $diffIstirahat = $standardIstirahat->diffInMinutes($selesai);
                $keterangan[] = "Terlambat Istirahat ({$diffIstirahat} menit)";
            }
        }

        if (! empty($keterangan)) {
            return implode(', ', $keterangan);
        }

        if ($this->status === 'hadir') {
            return 'Tepat Waktu';
        }

        if ($this->status === 'telat') {
            return 'Terlambat';
        }

        if ($this->status === 'alpha') {
            return 'Tanpa Keterangan';
        }

        if ($this->status === 'izin') {
            return 'Izin / Cuti';
        }

        return ucfirst($this->status);
    }
}
