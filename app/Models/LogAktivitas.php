<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'log_aktivitas';
    protected $primaryKey = 'id_log';
    protected $fillable = ['user_id', 'aksi', 'tabel', 'id_rekaman', 'perubahan'];
    protected $casts = ['perubahan' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
