<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function atasan(){
        return $this->belongsTo(User::class, 'atasan_id');
    }

    public function bawahan(){
        return $this->hasMany(User::class, 'atasan_id');
    }

    public function attendances(){
        return $this->hasMany(Attendance::class);
    }

    public function overtimeRequests(){
        return $this->hasMany(OvertimeRequest::class);
    }

    public function officeTrips(){
        return $this->hasMany(OfficeTrip::class);
    }
}
