<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalarySetting extends Model
{
    protected $fillable = ['rate_lembur_per_jam', 'potongan_per_hari_alpha'];
}
