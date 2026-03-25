<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoJob extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'titulo',
        'original_filename',
        'input_gcs_path',
        'output_video_url',
        'output_csv_url',
        'total_frames',
        'error_message',
        'processing_started_at',
        'processing_finished_at',
    ];

    protected $casts = [
        'processing_started_at'  => 'datetime',
        'processing_finished_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isProcessing(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }
}