<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomField extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'custom_fields';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'list_id',
        'name',
        'tag',
        'type',
        'options',
        'is_required',
        'default_value',
        'help_text',
        'sort_order',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Field type constants.
     */
    public const TYPE_TEXT = 'text';
    public const TYPE_NUMBER = 'number';
    public const TYPE_DATE = 'date';
    public const TYPE_DROPDOWN = 'dropdown';
    public const TYPE_CHECKBOX = 'checkbox';

    /**
     * Get the list that owns the custom field.
     */
    public function list(): BelongsTo
    {
        return $this->belongsTo(EmailList::class, 'list_id');
    }

    /**
     * Validate a value against this field's type.
     */
    public function validateValue($value): bool
    {
        if ($this->is_required && empty($value)) {
            return false;
        }

        if (empty($value)) {
            return true;
        }

        return match ($this->type) {
            self::TYPE_NUMBER => is_numeric($value),
            self::TYPE_DATE => strtotime($value) !== false,
            self::TYPE_DROPDOWN => in_array($value, $this->options ?? []),
            self::TYPE_CHECKBOX => is_bool($value) || in_array($value, ['0', '1', 'true', 'false']),
            default => true,
        };
    }

    /**
     * Format value based on field type.
     */
    public function formatValue($value): mixed
    {
        if (empty($value)) {
            return $this->default_value;
        }

        return match ($this->type) {
            self::TYPE_NUMBER => (float) $value,
            self::TYPE_DATE => date('Y-m-d', strtotime($value)),
            self::TYPE_CHECKBOX => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            default => (string) $value,
        };
    }

    /**
     * Get available field types.
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_TEXT => 'Text',
            self::TYPE_NUMBER => 'Number',
            self::TYPE_DATE => 'Date',
            self::TYPE_DROPDOWN => 'Dropdown',
            self::TYPE_CHECKBOX => 'Checkbox',
        ];
    }

    /**
     * Scope for active fields only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
