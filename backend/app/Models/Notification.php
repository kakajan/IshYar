<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Notification extends Model
{
    use HasFactory, HasUuids;
    use HasTranslations;

    public array $translatable = [
        'title',
        'body',
    ];

    protected $table = 'notifications_custom';

    /**
     * Notification types.
     */
    public const TYPE_TASK_ASSIGNED    = 'task_assigned';
    public const TYPE_TASK_COMPLETED   = 'task_completed';
    public const TYPE_TASK_COMMENT     = 'task_comment';
    public const TYPE_TASK_DUE_SOON    = 'task_due_soon';
    public const TYPE_TASK_OVERDUE     = 'task_overdue';
    public const TYPE_APPROVAL_REQUEST = 'approval_request';
    public const TYPE_APPROVAL_RESULT  = 'approval_result';
    public const TYPE_SYSTEM           = 'system';
    public const TYPE_ANNOUNCEMENT     = 'announcement';

    /**
     * Delivery channels.
     */
    public const CHANNEL_WEB      = 'web';
    public const CHANNEL_EMAIL    = 'email';
    public const CHANNEL_SMS      = 'sms';
    public const CHANNEL_TELEGRAM = 'telegram';
    public const CHANNEL_PUSH     = 'push';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'body',
        'data',
        'action_url',
        'channels',
        'delivery_status',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'data'            => 'array',
            'channels'        => 'array',
            'delivery_status' => 'array',
            'read_at'         => 'datetime',
        ];
    }

    /**
     * Get the user this notification belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark as read.
     */
    public function markAsRead(): self
    {
        $this->update(['read_at' => now()]);
        return $this;
    }

    /**
     * Check if notification is read.
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Scope for unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for specific type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
