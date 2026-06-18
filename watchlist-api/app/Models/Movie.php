<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'title',
        'external_id',
        'title',
        'year',
        'genre',
        'poster',
        'plot',
        'poster',
        'runtime',
        'imb_rating',
        'status',
    ];
}
