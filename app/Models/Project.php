<?php

namespace App\Models;

use App\Http\Traits\StaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use StaticTableName;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_ARCHIVED = 'archived';
    public const STATUS_BLOCKED = 'blocked';

    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'status',
        'default_sender_name',
        'default_from_email',
        'default_reply_to',
        'timezone',
        'locale',
        'unsubscribe_template_id',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function unsubscribeTemplate(): BelongsTo
    {
        return $this->belongsTo(Templates::class, 'unsubscribe_template_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusOptions()[$this->status] ?? $this->status;
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_ACTIVE => __('frontend.str.project_status_active'),
            self::STATUS_ARCHIVED => __('frontend.str.project_status_archived'),
            self::STATUS_BLOCKED => __('frontend.str.project_status_blocked'),
        ];
    }

    public static function statusValues(): array
    {
        return array_keys(self::statusOptions());
    }
}
