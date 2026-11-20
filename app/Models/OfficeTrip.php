<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeTrip extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal',
        'tujuan_alamat',
        'jam_keluar',
        'jam_kembali',
        'alasan',
        'alat_transportasi',
        'peran_kendaraan',
        'menumpang_user_id',
        'status',
        'approved_by',
        'approved_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function menumpangDengan()
    {
        return $this->belongsTo(User::class, 'menumpang_user_id');
    }
}
