<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Informasi extends Model
{
    protected $table = 'informasi';
    protected $fillable = [
        'judul', 'slug', 'deskripsi', 'isi', 'gambar', 'views', 'kategori'
    ];

    function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
}
