<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Label extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'organization_id',
        'name',
        'color',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Default colors for labels.
     */
    public const COLORS = [
        '#ef4444', // red
        '#f97316', // orange
        '#f59e0b', // amber
        '#eab308', // yellow
        '#84cc16', // lime
        '#22c55e', // green
        '#14b8a6', // teal
        '#06b6d4', // cyan
        '#0ea5e9', // sky
        '#3b82f6', // blue
        '#6366f1', // indigo
        '#8b5cf6', // violet
        '#a855f7', // purple
        '#d946ef', // fuchsia
        '#ec4899', // pink
        '#64748b', // slate
    ];

    /**
     * Get the organization this label belongs to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the tasks that have this label.
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'label_task')
            ->withTimestamps();
    }
}
