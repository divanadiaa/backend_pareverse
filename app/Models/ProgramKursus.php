<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramKursus extends Model
{
    protected $fillable = [
        'lembaga_id', 
        'nama_program', 
        'bahasa', 
        'harga', 
        'durasi',
    ];

    public function lembaga()
    {
        return $this->belongsTo(Lembaga::class);
    }
}
