<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuppressionList extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'suppression_list';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'brand_id',
        'email',
        'reason',
        'notes',
        'added_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the brand that owns the suppression entry.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Check if an email is suppressed for a brand.
     */
    public static function isSuppressed(int $brandId, string $email): bool
    {
        return self::where('brand_id', $brandId)
            ->where('email', strtolower($email))
            ->exists();
    }

    /**
     * Add an email to the suppression list.
     */
    public static function suppress(
        int $brandId,
        string $email,
        string $reason = 'manual',
        ?string $notes = null,
        ?string $addedBy = null
    ): self {
        return self::firstOrCreate(
            [
                'brand_id' => $brandId,
                'email' => strtolower($email),
            ],
            [
                'reason' => $reason,
                'notes' => $notes,
                'added_by' => $addedBy,
            ]
        );
    }
}
