<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OvertimeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'tanggal', 'jam_mulai', 'jam_selesai',
        'lokasi_lembur', 'alasan', 'status', 'approved_by', 
        'approved_at'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function approver(){
        return $this->belongsTo(User::class, 'approved_by');
    }
}
