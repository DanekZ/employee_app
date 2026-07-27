<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class LeaveRequest extends Model
{
    use HasFactory;

     protected $fillable = [
        'user_id', 'jenis', 'tujuan',
        'tanggal_mulai', 'tanggal_selesai',
        'durasi_menit', 'jam_mulai', 'jam_selesai',
        'keterangan', 'status', 'approved_by', 'approved_at',
    ];

     protected $casts = [
        'approved_at' => 'datetime',
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

}
