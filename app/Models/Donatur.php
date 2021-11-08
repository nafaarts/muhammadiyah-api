<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donatur extends Model
{
    protected $table = 'donatur';
    protected $fillable = ['nama', 'email', 'jumlah', 'private', 'donasi'];

    function donasi()
    {
        return $this->belongsTo(Donasi::class);
    }
}
