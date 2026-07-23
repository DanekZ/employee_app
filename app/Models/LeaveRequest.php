<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $fillable = [
        'user_id', 'tujuan', 'tanggal', 'keterangan', 'status',
        'approved_by', 'approved_at'
    ];

    public function User(){
        return $this->belongsTo(User::class);
    }

    public function approved(){
        return $this->belongsTo(User::class, 'approved_by');
    }
}
