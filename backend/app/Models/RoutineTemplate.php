<?php
namespace App\Models;

use App\Traits\TranslatableJsonSerialization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class RoutineTemplate extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    use HasTranslations;
    use TranslatableJsonSerialization;

    public array $translatable = [
        'name',
        'description',
    ];

    /**
     * Recurrence frequency types.
     */
    public const FREQUENCY_DAILY   = 'daily';
    public const FREQUENCY_WEEKLY  = 'weekly';
    public const FREQUENCY_MONTHLY = 'monthly';
    public const FREQUENCY_YEARLY  = 'yearly';
    public const FREQUENCY_CUSTOM  = 'custom';

    protected $fillable = [
        'organization_id',
        'creator_id',
        'name',
        'description',
        'frequency',
        'recurrence_rule',
        'default_checklist',
        'default_estimated_hours',
        'default_priority',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'recurrence_rule'         => 'array',
            'default_checklist'       => 'array',
            'default_estimated_hours' => 'integer',
            'is_active'               => 'boolean',
        ];
    }

    /**
     * Override name attribute to return translated value in JSON.
     */
    public function getNameAttribute($value)
    {
        if ($value && is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded[app()->getLocale()] ?? $decoded['en'] ?? $value;
            }
        }
        return $value;
    }

    /**
     * Override description attribute to return translated value in JSON.
     */
    public function getDescriptionAttribute($value)
    {
        if ($value && is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded[app()->getLocale()] ?? $decoded['en'] ?? $value;
            }
        }
        return $value;
    }

    /**
     * Get the organization.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the department.
     */
    /**
     * Get tasks generated from this template.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'routine_template_id');
    }

    /**
     * Generate a task instance from this template.
     */
    public function generateTask(): Task
    {
        $task = Task::create([
            'organization_id'     => $this->organization_id,
            'routine_template_id' => $this->id,
            'title'               => $this->name,
            'description'         => $this->description,
            'type'                => Task::TYPE_ROUTINE,
            'priority'            => $this->default_priority ?? Task::PRIORITY_MEDIUM,
            'estimated_hours'     => $this->default_estimated_hours,
            'checklist'           => $this->default_checklist,
            'is_recurring'        => true,
            'recurrence_rule'     => $this->recurrence_rule,
            'due_date'            => $this->calculateDueDate(),
        ]);

        // Intentionally no scheduling bookkeeping here yet;
        // this model is used mainly as a template source for tasks.

        return $task;
    }

    /**
     * Calculate due date based on frequency.
     */
    protected function calculateDueDate(): \Carbon\Carbon
    {
        return match ($this->frequency) {
            self::FREQUENCY_DAILY   => now()->endOfDay(),
            self::FREQUENCY_WEEKLY  => now()->endOfWeek(),
            self::FREQUENCY_MONTHLY => now()->endOfMonth(),
            self::FREQUENCY_YEARLY  => now()->endOfYear(),
            default                 => now()->addDays(1),
        };
    }

    /**
     * Calculate next run time.
     */
}
