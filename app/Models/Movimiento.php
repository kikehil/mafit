<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Movimiento extends Model
{
    protected $fillable = [
        'user_id',
        'tipo_movimiento',
        'cr',
        'tienda',
        'plaza',
        'nombre_plaza',
        'equipo_retirado_placa',
        'equipo_retirado_serie',
        'equipo_retirado_descripcion',
        'equipo_retirado_marca',
        'equipo_retirado_modelo',
        'equipo_retirado_activo',
        'equipo_retirado_remanente',
        'equipo_retirado_inventariotda_id',
        'equipo_retirado_maf_id',
        'equipo_remplazo_placa',
        'equipo_remplazo_serie',
        'equipo_remplazo_descripcion',
        'equipo_remplazo_marca',
        'equipo_remplazo_modelo',
        'equipo_remplazo_activo',
        'equipo_remplazo_remanente',
        'equipo_remplazo_inventariotda_id',
        'equipo_remplazo_maf_id',
        'equipo_agregado_placa',
        'equipo_agregado_serie',
        'equipo_agregado_descripcion',
        'equipo_agregado_marca',
        'equipo_agregado_modelo',
        'equipo_agregado_activo',
        'equipo_agregado_remanente',
        'equipo_agregado_inventariotda_id',
        'motivo',
        'comentarios',
        'seguimiento',
        'realizo_inventario',
    ];

    protected $casts = [
        'equipo_retirado_remanente' => 'decimal:2',
        'equipo_remplazo_remanente' => 'decimal:2',
        'equipo_agregado_remanente' => 'decimal:2',
        'realizo_inventario' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function equipoRetiradoInventariotda(): BelongsTo
    {
        return $this->belongsTo(Inventariotda::class, 'equipo_retirado_inventariotda_id');
    }

    public function equipoRetiradoMaf(): BelongsTo
    {
        return $this->belongsTo(Maf::class, 'equipo_retirado_maf_id');
    }

    public function equipoRemplazoInventariotda(): BelongsTo
    {
        return $this->belongsTo(Inventariotda::class, 'equipo_remplazo_inventariotda_id');
    }

    public function equipoRemplazoMaf(): BelongsTo
    {
        return $this->belongsTo(Maf::class, 'equipo_remplazo_maf_id');
    }

    public function equipoAgregadoInventariotda(): BelongsTo
    {
        return $this->belongsTo(Inventariotda::class, 'equipo_agregado_inventariotda_id');
    }
}
