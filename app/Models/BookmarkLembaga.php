<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookmarkLembaga extends Model
{
    protected $fillable = [
        'user_id', 'lembaga_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lembaga()
    {
        return $this->belongsTo(Lembaga::class);
    }
}
