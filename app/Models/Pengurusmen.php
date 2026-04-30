<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengurusmen extends Model
{
    protected $table = 'pengurusmen';
    protected $fillable = ['nama', 'jabatan', 'divisi', 'foto', 'angkatan', 'instagram', 'linkedin', 'urut', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
