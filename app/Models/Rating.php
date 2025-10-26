<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'user_id',
        'voter_name',
        'dimension',
        'score',
        'notes',
        'visited_at',
        'voting_session_id',
    ];

    protected $casts = [
        'score' => 'integer',
        'visited_at' => 'datetime',
    ];

    /**
     * Get the restaurant this rating belongs to
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Get the user who made this rating
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the voting session this rating belongs to
     */
    public function votingSession(): BelongsTo
    {
        return $this->belongsTo(VotingSession::class);
    }

    /**
     * Get the display name for the voter
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->user_id && $this->user) {
            return $this->user->name;
        }
        return $this->voter_name ?? 'Anonymous';
    }

    /**
     * Validate score is between 1-5
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if ($model->score < 1 || $model->score > 5) {
                throw new \InvalidArgumentException('Score must be between 1 and 5');
            }
        });

        static::updating(function ($model) {
            if ($model->score < 1 || $model->score > 5) {
                throw new \InvalidArgumentException('Score must be between 1 and 5');
            }
        });
    }
}
