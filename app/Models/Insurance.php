<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Insurance extends Model
{
    protected $fillable = [
        'name'
    ];

    public function lead(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
