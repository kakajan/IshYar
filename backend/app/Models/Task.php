<?php
namespace App\Models;

use App\Traits\TranslatableJsonSerialization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Task extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    use HasTranslations;
    use TranslatableJsonSerialization;

    public array $translatable = [
        'title',
        'description',
    ];

    /**
     * Task types.
     */
    public const TYPE_ROUTINE     = 'routine';
    public const TYPE_SITUATIONAL = 'situational';

    /**
     * Task statuses.
     */
    public const STATUS_PENDING        = 'pending';
    public const STATUS_IN_PROGRESS    = 'in_progress';
    public const STATUS_PENDING_REVIEW = 'pending_review';
    public const STATUS_COMPLETED      = 'completed';
    public const STATUS_ON_HOLD        = 'on_hold';
    public const STATUS_CANCELLED      = 'cancelled';

    /**
     * Approval statuses.
     */
    public const APPROVAL_NOT_REQUIRED = 'not_required';
    public const APPROVAL_PENDING      = 'pending';
    public const APPROVAL_APPROVED     = 'approved';
    public const APPROVAL_REJECTED     = 'rejected';

    /**
     * Priority levels.
     */
    public const PRIORITY_LOW    = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH   = 'high';
    public const PRIORITY_URGENT = 'urgent';

    protected $fillable = [
        'organization_id',
        'parent_id',
        'routine_template_id',
        'title',
        'description',
        'type',
        'status',
        'priority',
        'creator_id',
        'assignee_id',
        'department_id',
        'due_date',
        'started_at',
        'completed_at',
        'estimated_hours',
        'actual_hours',
        'progress',
        'checklist',
        'attachments',
        'metadata',
        'is_recurring',
        'recurrence_rule',
        'approval_status',
        'approved_by',
        'approved_at',
        'revision_count',
        'revision_notes',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date'        => 'datetime',
            'started_at'      => 'datetime',
            'completed_at'    => 'datetime',
            'approved_at'     => 'datetime',
            'estimated_hours' => 'decimal:2',
            'actual_hours'    => 'decimal:2',
            'progress'        => 'integer',
            'checklist'       => 'array',
            'attachments'     => 'array',
            'metadata'        => 'array',
            'recurrence_rule' => 'array',
            'is_recurring'    => 'boolean',
            'revision_notes'  => 'array',
            'revision_count'  => 'integer',
            'submitted_at'    => 'datetime',
        ];
    }

    /**
     * Override title attribute to return translated value in JSON.
     */
    public function getTitleAttribute($value)
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
     * Get the organization this task belongs to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get parent task.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    /**
     * Get subtasks.
     */
    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    /**
     * Get the routine template.
     */
    public function routineTemplate(): BelongsTo
    {
        return $this->belongsTo(RoutineTemplate::class);
    }

    /**
     * Get the task creator.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Get the task assignee.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /**
     * Get the department.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the approver.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get task dependencies.
     */
    public function dependencies(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'task_id', 'depends_on_id')
            ->withPivot('dependency_type')
            ->withTimestamps();
    }

    /**
     * Get tasks that depend on this task.
     */
    public function dependents(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'depends_on_id', 'task_id')
            ->withPivot('dependency_type')
            ->withTimestamps();
    }

    /**
     * Get time entries for this task.
     */
    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    /**
     * Get task comments.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }

    /**
     * Get task labels.
     */
    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_assignees', 'task_id', 'user_id');
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'label_task')
            ->using(LabelTask::class)
            ->withTimestamps();
    }

    /**
     * Scope for routine tasks.
     */
    public function scopeRoutine($query)
    {
        return $query->where('type', self::TYPE_ROUTINE);
    }

    /**
     * Scope for situational tasks.
     */
    public function scopeSituational($query)
    {
        return $query->where('type', self::TYPE_SITUATIONAL);
    }

    /**
     * Scope for pending tasks.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for overdue tasks.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    /**
     * Check if task is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_date &&
        $this->due_date->isPast() &&
        ! in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    /**
     * Check if task is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Mark task as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status'       => self::STATUS_COMPLETED,
            'completed_at' => now(),
            'progress'     => 100,
        ]);
    }

    /**
     * Submit task for approval.
     */
    public function submitForApproval(): void
    {
        $this->update([
            'status'          => self::STATUS_PENDING_REVIEW,
            'approval_status' => self::APPROVAL_PENDING,
            'progress'        => 100,
            'submitted_at'    => now(),
        ]);
    }

    /**
     * Approve task completion.
     */
    public function approve(string $approverId): void
    {
        $this->update([
            'status'          => self::STATUS_COMPLETED,
            'approval_status' => self::APPROVAL_APPROVED,
            'approved_by'     => $approverId,
            'approved_at'     => now(),
            'completed_at'    => now(),
        ]);
    }

    /**
     * Request revision on task.
     */
    public function requestRevision(string $reason, ?\DateTime $deadline = null): void
    {
        $revisionNotes = $this->revision_notes ?? [];
        $revisionNotes[] = [
            'reason'     => $reason,
            'created_at' => now()->toIso8601String(),
            'created_by' => auth()->id(),
        ];

        $this->update([
            'status'          => self::STATUS_IN_PROGRESS,
            'approval_status' => self::APPROVAL_REJECTED,
            'revision_count'  => ($this->revision_count ?? 0) + 1,
            'revision_notes'  => $revisionNotes,
            'due_date'        => $deadline ?? $this->due_date,
            'submitted_at'    => null,
        ]);
    }

    /**
     * Check if task is pending review.
     */
    public function isPendingReview(): bool
    {
        return $this->status === self::STATUS_PENDING_REVIEW;
    }

    /**
     * Check if task requires approval.
     */
    public function requiresApproval(): bool
    {
        return $this->approval_status !== self::APPROVAL_NOT_REQUIRED;
    }

    /**
     * Scope for tasks pending review.
     */
    public function scopePendingReview($query)
    {
        return $query->where('status', self::STATUS_PENDING_REVIEW);
    }

    /**
     * Get the designated approver (manager or task creator).
     */
    public function getApprover(): ?User
    {
        // First try assignee's manager
        if ($this->assignee && $this->assignee->manager) {
            return $this->assignee->manager;
        }

        // Fall back to task creator
        return $this->creator;
    }
}
