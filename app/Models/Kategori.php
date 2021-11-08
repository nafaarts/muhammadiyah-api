<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $table = "kategori";

    protected $fillable = ['kategori'];

    function informasi()
    {
        return $this->HasMany(Informasi::class);
    }
}
