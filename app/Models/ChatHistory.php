<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    protected $fillable = ['session_id', 'role', 'content', 'sources'];
    protected $casts    = ['sources' => 'array'];
}
