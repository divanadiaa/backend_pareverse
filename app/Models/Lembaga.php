<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lembaga extends Model
{
    protected $fillable = [
        'nama', 
        'deskripsi', 
        'gambar', 
        'alamat', 
        'link_maps', 
        'whatsapp', 
        'is_recommended',
    ];

    public function programKursuses()
    {
        return $this->hasMany(ProgramKursus::class, 'lembaga_id');
    }

    public function reviews()
    {
        return $this->hasMany(ReviewLembaga::class, 'lembaga_id');
    }
}
