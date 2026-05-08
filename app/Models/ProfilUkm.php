<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilUkm extends Model
{
    protected $table = 'profil_ukm';

    protected $fillable = [
        'nama_ukm',
        'tagline',
        'deskripsi',
        'visi',
        'misi',
        'keunggulan',
    ];

    protected $casts = [
        'keunggulan' => 'array',
    ];
}
