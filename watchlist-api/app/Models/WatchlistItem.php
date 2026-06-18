<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatchlistItem extends Model
{
    protected $fillable = [
        'movie_id',
        'user_id',
        'status',
        'rating',
        'notes',
    ];

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class, 'movie_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
