<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ad extends Model
{
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }
}
