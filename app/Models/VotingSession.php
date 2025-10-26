<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VotingSession extends Model
{
    protected $fillable = [
        'user_id',
        'restaurant_id',
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function averageScorePerDimension(): array
    {
        $dimensions = ['taste', 'service', 'atmosphere', 'value'];
        $averages = [];

        foreach ($dimensions as $dimension) {
            $averages[$dimension] = round(
                $this->ratings()
                    ->where('dimension', $dimension)
                    ->avg('score') ?? 0,
                1
            );
        }

        return $averages;
    }

    public function overallScore(): float
    {
        return round(
            $this->ratings()->avg('score') ?? 0,
            1
        );
    }
}
