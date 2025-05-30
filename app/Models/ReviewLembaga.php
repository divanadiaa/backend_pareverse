<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewLembaga extends Model
{
    protected $fillable = [
        'user_id', 
        'lembaga_id', 
        'rating', 
        'komentar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lembaga()
    {
        return $this->belongsTo(Lembaga::class);
    }
}
