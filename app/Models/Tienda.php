<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tienda extends Model
{
    protected $fillable = [
        'plaza',
        'cr',
        'tienda',
    ];

    public function plazaRef(): BelongsTo
    {
        return $this->belongsTo(Plaza::class, 'plaza', 'plaza');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_tienda');
    }

    public function getDisplayNameAttribute(): string
    {
        $nombre = $this->tienda ?: $this->cr;
        return "{$this->cr} - {$nombre}";
    }
}
