<?php

namespace App\Models;

use App\Enums\AccessLevel;
use App\Enums\DocumentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'document_number',
        'document_type_id',
        'category_id',
        'stage_id',
        'year',
        'document_date',
        'status',
        'access_level',
        'description',
        'keywords',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'document_date' => 'date',
            'status' => DocumentStatus::class,
            'access_level' => AccessLevel::class,
        ];
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class)->orderByDesc('version_number');
    }

    public function latestVersion(): HasOne
    {
        return $this->hasOne(DocumentVersion::class)->latestOfMany('version_number');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function isArchived(): bool
    {
        return $this->status === DocumentStatus::Archived || $this->trashed();
    }

    public function statusLabel(): string
    {
        return $this->status?->label() ?? (string) $this->status;
    }

    public function scopeSearch($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('document_number', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('keywords', 'like', "%{$search}%")
                ->orWhereHas('category', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                ->orWhereHas('documentType', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                ->orWhereHas('stage', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        });
    }

    public function scopeFilterByAccessLevel($query, ?string $level): void
    {
        if ($level !== null && $level !== '') {
            $query->where('access_level', $level);
        }
    }

    public function scopeFilterByDateRange($query, ?string $from, ?string $to): void
    {
        if ($from !== null && $from !== '') {
            $query->where('document_date', '>=', $from);
        }
        if ($to !== null && $to !== '') {
            $query->where('document_date', '<=', $to);
        }
    }
}