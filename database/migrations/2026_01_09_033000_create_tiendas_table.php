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
        Schema::create('tiendas', function (Blueprint $table) {
            $table->id();
            $table->string('plaza', 20);
            $table->string('cr', 50);
            $table->string('tienda', 255)->nullable();
            $table->timestamps();

            // Foreign key a plazas
            $table->foreign('plaza')
                ->references('plaza')
                ->on('plazas')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            // Índice único para evitar duplicados de plaza+cr
            $table->unique(['plaza', 'cr']);
            $table->index('plaza');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiendas');
    }
};
