<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhoneVerification extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'phone',
        'code',
        'expires_at',
        'verified_at',
        'attempts',
    ];

    protected function casts(): array
    {
        return [
            'expires_at'  => 'datetime',
            'verified_at' => 'datetime',
            'attempts'    => 'integer',
        ];
    }

    /**
     * Get the user associated with this verification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the verification code has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the verification has been used.
     */
    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    /**
     * Check if too many attempts have been made.
     */
    public function hasExceededAttempts(int $maxAttempts = 5): bool
    {
        return $this->attempts >= $maxAttempts;
    }

    /**
     * Increment the attempt counter.
     */
    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }

    /**
     * Mark as verified.
     */
    public function markAsVerified(): void
    {
        $this->update(['verified_at' => now()]);
    }

    /**
     * Scope for pending (not yet verified) verifications.
     */
    public function scopePending($query)
    {
        return $query->whereNull('verified_at');
    }

    /**
     * Scope for valid (not expired, not verified) verifications.
     */
    public function scopeValid($query)
    {
        return $query->pending()
            ->where('expires_at', '>', now());
    }

    /**
     * Create a new verification for a phone number.
     */
    public static function createFor(string $phone, ?string $userId = null, int $expiryMinutes = 5): self
    {
        // Invalidate any existing pending verifications for this phone
        static::where('phone', $phone)
            ->pending()
            ->delete();

        return static::create([
            'user_id'    => $userId,
            'phone'      => $phone,
            'code'       => \App\Services\Sms\IPPanelService::generateOtpCode(),
            'expires_at' => now()->addMinutes($expiryMinutes),
        ]);
    }
}
