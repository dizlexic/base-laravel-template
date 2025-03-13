<?php

namespace App\Models\Morphs;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Taggable extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tag_id',
        'taggable_id',
        'taggable_type',
    ];

    protected function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class, 'tag_id');
    }

    protected function taggable(): MorphTo
    {
        return $this->morphTo();
    }
}
