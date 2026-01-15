<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maf extends Model
{
    protected $table = 'maf';

    protected $fillable = [
        'batch_id',
        'row_num',
        'plaza',
        'cr',
        'tienda',
        'placa',
        'activo',
        'mescompra',
        'anocompra',
        'valor_neto',
        'remanente',
        'descripcion',
        'categoria',
        'marca',
        'modelo',
        'serie',
        'imported_at',
    ];

    protected $casts = [
        'valor_neto' => 'decimal:2',
        'remanente' => 'decimal:2',
        'imported_at' => 'datetime',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(MafImportBatch::class, 'batch_id');
    }

    public function plazaRelation(): BelongsTo
    {
        return $this->belongsTo(Plaza::class, 'plaza', 'plaza');
    }
}








