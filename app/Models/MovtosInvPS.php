<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovtosInvPS extends Model
{
    protected $table = 'movtosinvps';

    protected $fillable = [
        'inventario_psf_id',
        'user_id',
        'tipo_movimiento',
        'ubicacion_anterior',
        'ubicacion_nueva',
        'notas',
    ];

    public function inventarioPSF(): BelongsTo
    {
        return $this->belongsTo(InventarioPSF::class, 'inventario_psf_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
