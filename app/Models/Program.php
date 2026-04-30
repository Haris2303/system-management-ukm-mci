<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'programs';
    protected $fillable = ['nama', 'deskripsi', 'icon', 'kategori', 'is_active', 'urut'];
    protected $casts = ['is_active' => 'boolean'];

    public function scopeAktif($q)
    {
        return $q->where('is_active', true);
    }
}
