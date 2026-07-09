<?php

namespace App\Models;

use App\Http\Traits\StaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use StaticTableName;

    protected $fillable = [
        'name',
        'description',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
