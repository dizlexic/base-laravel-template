<?php

namespace App\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    protected function is_verified(): bool
    {
        return $this->verified_by !== null;
    }

    protected function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'taggable');
    }

    protected function verified_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
