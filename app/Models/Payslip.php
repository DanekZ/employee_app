<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payslip extends Model
{
    protected $fillable = [
    'user_id', 'periode', 'gaji_pokok', 'total_jam_lembur', 'nominal_lembur',
    'jumlah_hari_alpha', 'potongan_absen', 'jumlah_hari_dinas', 'nominal_dinas',
    'jumlah_hari_izin', 'potongan_izin', 'pph21', 'total_gaji_bersih',
    'status', 'diterbitkan_at',
];

      protected $casts = [
        'diterbitkan_at' => 'datetime',
    ];

      public function user()
    {
        return $this->belongsTo(User::class);
    }
}
