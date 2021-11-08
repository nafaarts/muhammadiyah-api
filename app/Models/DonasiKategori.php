<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonasiKategori extends Model
{
    protected $table = 'donasi_kategori';
    protected $fillable = ['kategori'];

    function donasi()
    {
        return $this->hasMany(Donasi::class);
    }
}
