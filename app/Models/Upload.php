<?php

namespace App\Models;

use Database\Factories\UploadFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Upload extends Model
{
    /** @use HasFactory<UploadFactory> */
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uploaded_by',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'file_extension',
        'verified_by',
    ];

    protected function is_verified(): bool
    {
        return $this->verified_by !== null;
    }

    protected function uploaded_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    protected function verified_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
