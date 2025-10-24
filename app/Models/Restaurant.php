<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'notes',
    ];

    /**
     * Get all ratings for this restaurant
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
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
