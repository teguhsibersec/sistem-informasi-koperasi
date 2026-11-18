<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'username', 'email', 'password', 'role', 'id_pengurus', 'id_anggota'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed'];
    }

    public function pengurus()
    {
        return $this->belongsTo(Pengurus::class, 'id_pengurus');
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'id_anggota');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAnggota(): bool
    {
        return $this->role === 'anggota';
    }

    /** admin/ketua = semua akses; selain itu cek peran pengurus (kasir/gudang). */
    public function punyaPeran(string ...$peran): bool
    {
        if ($this->isAdmin() || $this->pengurus?->jabatan_pengurus === 'ketua') {
            return true;
        }

        return $this->role === 'pengurus' && in_array(Pengurus::AKSES[$this->pengurus?->jabatan_pengurus] ?? null, $peran, true);
    }
}
