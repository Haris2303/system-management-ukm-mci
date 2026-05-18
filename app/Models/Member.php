<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'members';
    protected $fillable = ['nama_lengkap', 'nim', 'email', 'no_hp', 'jurusan', 'angkatan', 'motivasi', 'status', 'foto'];
}
