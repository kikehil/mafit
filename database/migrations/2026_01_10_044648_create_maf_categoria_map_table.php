<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('maf_categoria_map', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion_key', 190)->unique()->comment('Descripción normalizada (llave)');
            $table->string('descripcion_raw', 255)->comment('Descripción original legible (para UI)');
            $table->string('categoria', 80);
            $table->tinyInteger('activo')->default(1);
            $table->timestamps();

            $table->index('categoria');
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maf_categoria_map');
    }
};
