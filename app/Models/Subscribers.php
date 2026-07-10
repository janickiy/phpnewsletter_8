<?php

namespace App\Models;


use App\Http\Traits\StaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscribers extends Model
{
    use HasFactory, Notifiable, StaticTableName;

    protected $table = 'subscribers';

    protected $fillable = [
        'name',
        'email',
        'active',
        'timeSent',
        'token'
    ];

    protected $hidden = [
        'token',
    ];

    /**
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('active', 'true');
    }

    /**
     * @return HasMany
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscriptions::class, 'subscriber_id');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_subscriber', 'subscriber_id', 'project_id')
            ->withTimestamps();
    }

    /**
     * @return void
     */
    public function scopeRemove(): void
    {
        foreach ($this->subscriptions ?? [] as $subscription) {
            $subscription->delete();
        }

        $this->delete();
    }
}
