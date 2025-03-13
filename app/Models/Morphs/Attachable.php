<?php

namespace App\Models\Morphs;

use App\Models\Upload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachable extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'attachable_id',
        'attachable_type',
        'attachment_id',
    ];

    protected function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    protected function attachment(): BelongsTo
    {
        return $this->belongsTo(Upload::class, 'attachment_id');
    }
}
