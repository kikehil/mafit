<?php

namespace App\Models;

use App\Helpers\TextNorm;
use Illuminate\Database\Eloquent\Model;

class MafCategoriaMap extends Model
{
    protected $table = 'maf_categoria_map';

    protected $fillable = [
        'descripcion_key',
        'descripcion_raw',
        'categoria',
        'activo',
    ];

    protected $casts = [
        'activo' => 'integer',
    ];

    /**
     * Boot del modelo: auto-calcular descripcion_key cuando cambia descripcion_raw
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->isDirty('descripcion_raw')) {
                $model->descripcion_key = TextNorm::key($model->descripcion_raw);
            }
        });
    }

    /**
     * Scope para solo activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }

    /**
     * Buscar categoría por descripción normalizada
     */
    public static function buscarCategoria(string $descripcion): ?string
    {
        $key = TextNorm::key($descripcion);
        $map = self::where('descripcion_key', $key)
            ->where('activo', 1)
            ->first();

        return $map?->categoria;
    }
}
