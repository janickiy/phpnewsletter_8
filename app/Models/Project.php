<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use App\Enums\UserRole;
use App\Http\Traits\StaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use StaticTableName;

    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'status',
        'default_sender_name',
        'default_from_email',
        'default_reply_to',
        'timezone',
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

    public function templates(): HasMany
    {
        return $this->hasMany(Templates::class);
    }

    public function administrators(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_admins')
            ->withPivot('role')
            ->wherePivot('role', UserRole::ProjectAdmin->value)
            ->withTimestamps();
    }

    public function moderators(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_admins')
            ->withPivot('role')
            ->wherePivot('role', UserRole::Moderator->value)
            ->withTimestamps();
    }

    public function projectUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_admins')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(Subscribers::class, 'project_subscriber', 'project_id', 'subscriber_id')
            ->withTimestamps();
    }

    public function getStatusLabelAttribute(): string
    {
        return ProjectStatus::labelFor($this->status);
    }

    public static function statusOptions(): array
    {
        return ProjectStatus::options();
    }

    public static function statusValues(): array
    {
        return ProjectStatus::values();
    }
}
