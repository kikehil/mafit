<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plaza extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'plaza';

    protected $fillable = [
        'plaza',
        'plaza_nom',
    ];

    public function mafRecords()
    {
        return $this->hasMany(Maf::class, 'plaza', 'plaza');
    }
}











