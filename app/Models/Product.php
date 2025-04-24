<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = true;
    protected $table = 'produks';
    protected $fillable = [
        'nama',
        'deskripsi',
        'harga',
        'password'
    ];
}
