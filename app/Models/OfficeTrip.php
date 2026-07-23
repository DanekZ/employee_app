<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeTrip extends Model
{
      protected $fillable = [
        'user_id','tanggal', 'tujuan_alamat', 'jam_keluar', 'jam_kembali',
        'alasan', 'alat_transportasi', 'status', 'approved_by', 'approved_at',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function approver(){
        return $this->belongsTo(User::class, 'approved_by');
    }
}
