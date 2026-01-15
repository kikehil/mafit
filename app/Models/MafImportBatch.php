<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MafImportBatch extends Model
{
    protected $table = 'maf_import_batches';

    protected $fillable = [
        'period',
        'filename',
        'uploaded_by',
        'status',
        'started_at',
        'finished_at',
        'total_rows',
        'inserted_rows',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function mafRecords(): HasMany
    {
        return $this->hasMany(Maf::class, 'batch_id');
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isDone(): bool
    {
        return $this->status === 'done';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}











