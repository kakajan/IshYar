<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoutineTemplate extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

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
        'department_id',
        'title',
        'description',
        'priority',
        'frequency',
        'recurrence_rule',
        'time_of_day',
        'day_of_week',
        'day_of_month',
        'assignee_id',
        'assignee_role',
        'estimated_hours',
        'checklist',
        'requires_approval',
        'approval_chain',
        'is_active',
        'next_run_at',
        'last_run_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'recurrence_rule'   => 'array',
            'checklist'         => 'array',
            'approval_chain'    => 'array',
            'metadata'          => 'array',
            'requires_approval' => 'boolean',
            'is_active'         => 'boolean',
            'next_run_at'       => 'datetime',
            'last_run_at'       => 'datetime',
            'time_of_day'       => 'datetime',
            'estimated_hours'   => 'decimal:2',
        ];
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
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the default assignee.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

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
            'title'               => $this->title,
            'description'         => $this->description,
            'type'                => Task::TYPE_ROUTINE,
            'priority'            => $this->priority ?? Task::PRIORITY_MEDIUM,
            'assignee_id'         => $this->assignee_id,
            'department_id'       => $this->department_id,
            'estimated_hours'     => $this->estimated_hours,
            'checklist'           => $this->checklist,
            'is_recurring'        => true,
            'recurrence_rule'     => $this->recurrence_rule,
            'due_date'            => $this->calculateDueDate(),
        ]);

        $this->update([
            'last_run_at' => now(),
            'next_run_at' => $this->calculateNextRunAt(),
        ]);

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
    protected function calculateNextRunAt(): \Carbon\Carbon
    {
        return match ($this->frequency) {
            self::FREQUENCY_DAILY   => now()->addDay(),
            self::FREQUENCY_WEEKLY  => now()->addWeek(),
            self::FREQUENCY_MONTHLY => now()->addMonth(),
            self::FREQUENCY_YEARLY  => now()->addYear(),
            default                 => now()->addDay(),
        };
    }
}
