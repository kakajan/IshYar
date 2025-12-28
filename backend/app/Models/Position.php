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

class Position extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    use HasTranslations;
    use TranslatableJsonSerialization;

    public array $translatable = [
        'name',
        'description',
    ];

    protected $fillable = [
        'organization_id',
        'department_id',
        'name',
        'slug',
        'description',
        'level',
        'is_manager',
        'responsibilities',
        'requirements',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active'        => 'boolean',
            'is_manager'       => 'boolean',
            'level'            => 'integer',
            'responsibilities' => 'array',
            'requirements'     => 'array',
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
     * Get the organization this position belongs to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the department this position belongs to.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get all users with this position.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
