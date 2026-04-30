<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $table = 'gallery';
    protected $fillable = ['judul', 'foto', 'kategori', 'deskripsi', 'is_featured', 'urut'];
    protected $casts = ['is_featured' => 'boolean'];
}
