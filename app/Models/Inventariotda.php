<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventariotda extends Model
{
    protected $table = 'inventariotda';

    protected $fillable = [
        'maf_id',
        'user_id',
        'cr',
        'tienda',
        'placa_editada',
        'marca_editada',
        'modelo_editado',
        'serie_editada',
        'notas',
        'estado',
        'seguimiento',
        'en_garantia',
        'fecha_inventario',
    ];

    protected $casts = [
        'fecha_inventario' => 'datetime',
    ];

    public function maf(): BelongsTo
    {
        return $this->belongsTo(Maf::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
