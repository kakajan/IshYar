<?php
namespace App\Models;

use App\Traits\TranslatableJsonSerialization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Organization extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    use HasTranslations;
    use TranslatableJsonSerialization;

    public array $translatable = [
        'name',
        'description',
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'timezone',
        'default_locale',
        'default_currency',
        'settings',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'settings'  => 'array',
            'is_active' => 'boolean',
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
     * Get all departments for this organization.
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Get all positions for this organization.
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    /**
     * Get all users for this organization.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all tasks for this organization.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
