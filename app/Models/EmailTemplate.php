<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['brand_id', 'name', 'description', 'html_content', 'plain_text_content', 'thumbnail', 'is_public', 'is_active'];
    protected $casts = ['is_public' => 'boolean', 'is_active' => 'boolean'];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
}
