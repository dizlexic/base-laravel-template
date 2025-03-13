<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'content',
        'status',
        'type',
        'slug',
        'user_id',
    ];

    protected function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    protected function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
