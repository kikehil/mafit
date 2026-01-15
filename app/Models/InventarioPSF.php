<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventarioPSF extends Model
{
    protected $table = 'inventario_psf';

    protected $fillable = [
        'user_id',
        'maf_id',
        'plaza',
        'nombre_plaza',
        'cr',
        'nombre_tienda',
        'placa',
        'marca',
        'modelo',
        'serie',
        'activo',
        'anocompra',
        'valor_neto',
        'remanente',
        'ubicacion',
        'plaza_usuario',
        'notas',
        'encontrado_en_maf',
        'activo_registro',
    ];

    protected $casts = [
        'valor_neto' => 'decimal:2',
        'remanente' => 'decimal:2',
        'anocompra' => 'integer',
        'encontrado_en_maf' => 'boolean',
        'activo_registro' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function maf(): BelongsTo
    {
        return $this->belongsTo(Maf::class);
    }

    public function movimientos()
    {
        return $this->hasMany(MovtosInvPS::class, 'inventario_psf_id');
    }
}
