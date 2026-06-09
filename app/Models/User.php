<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Concerns\UsesIndonesianUserColumns;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, UsesIndonesianUserColumns;

    protected $table = 'pengguna';

    protected $fillable = ['nama', 'name', 'email', 'email_diverifikasi_pada', 'email_verified_at', 'kata_sandi', 'password', 'peran', 'role', 'aktif'];
    protected $hidden = ['kata_sandi', 'password', 'token_ingat'];
    protected $casts = [
        'email_diverifikasi_pada' => 'datetime',
        'kata_sandi' => 'hashed',
        'aktif' => 'boolean',
    ];

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'pengguna_id');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isEmployee()
    {
        return $this->role === 'employee';
    }

    public function getAuthPassword()
    {
        return $this->kata_sandi;
    }

    public function getRememberTokenName()
    {
        return 'token_ingat';
    }
}
