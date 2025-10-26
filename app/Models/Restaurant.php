<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'notes',
        'slug',
        'voting_enabled',
        'user_id',
    ];

    protected $casts = [
        'voting_enabled' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($restaurant) {
            if (!$restaurant->slug) {
                $restaurant->slug = Str::slug($restaurant->name);
            }
        });

        static::updating(function ($restaurant) {
            if ($restaurant->isDirty('name') && !$restaurant->isDirty('slug')) {
                $restaurant->slug = Str::slug($restaurant->name);
            }
        });
    }

    /**
     * Get the user who created this restaurant
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all ratings for this restaurant
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get all voting sessions for this restaurant
     */
    public function votingSessions(): HasMany
    {
        return $this->hasMany(VotingSession::class);
    }

    /**
     * Calculate average score per dimension
     */
    public function averageScorePerDimension(): array
    {
        return $this->ratings()
            ->selectRaw('dimension, ROUND(AVG(score), 2) as average')
            ->groupBy('dimension')
            ->pluck('average', 'dimension')
            ->toArray();
    }

    /**
     * Calculate overall score (average of all dimension averages)
     */
    public function overallScore(): float
    {
        $averages = $this->averageScorePerDimension();
        if (empty($averages)) {
            return 0;
        }
        return round(array_sum($averages) / count($averages), 2);
    }

    /**
     * Get visit count
     */
    public function visitCount(): int
    {
        return $this->ratings()->distinct('visitor_id')->count();
    }
}
