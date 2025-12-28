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

class Department extends Model
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
        'parent_id',
        'name',
        'slug',
        'description',
        'code',
        'head_id',
        'sort_order',
        'is_active',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'metadata'   => 'array',
            'sort_order' => 'integer',
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
     * Get the organization this department belongs to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the parent department.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    /**
     * Get child departments.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Department::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Get the department head.
     */
    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_id');
    }

    /**
     * Get all users in this department.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all positions in this department.
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    /**
     * Get all descendants (recursive children).
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get all ancestors (recursive parents).
     */
    public function ancestors()
    {
        $ancestors = collect();
        $parent    = $this->parent;

        while ($parent) {
            $ancestors->push($parent);
            $parent = $parent->parent;
        }

        return $ancestors;
    }
}
