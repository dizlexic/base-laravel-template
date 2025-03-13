<?php

namespace App\Models\Morphs;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Commentable extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'comment_id',
        'commentable_id',
        'commentable_type',
    ];

    protected function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }

    protected function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}
