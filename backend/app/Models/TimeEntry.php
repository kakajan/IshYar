<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeEntry extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'task_id',
        'user_id',
        'description',
        'started_at',
        'ended_at',
        'duration_minutes',
        'is_billable',
    ];

    protected function casts(): array
    {
        return [
            'started_at'       => 'datetime',
            'ended_at'         => 'datetime',
            'duration_minutes' => 'integer',
            'is_billable'      => 'boolean',
        ];
    }

    /**
     * Get the task this time entry belongs to.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user who logged this time.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate duration if ended_at is set.
     */
    public function getDurationAttribute(): ?int
    {
        if ($this->duration_minutes) {
            return $this->duration_minutes;
        }

        if ($this->started_at && $this->ended_at) {
            return $this->started_at->diffInMinutes($this->ended_at);
        }

        return null;
    }

    /**
     * Stop the timer and calculate duration.
     */
    public function stop(): self
    {
        $this->ended_at         = now();
        $this->duration_minutes = $this->started_at->diffInMinutes($this->ended_at);
        $this->save();

        return $this;
    }
}
