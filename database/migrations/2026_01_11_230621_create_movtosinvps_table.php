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
        Schema::create('movtosinvps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventario_psf_id')->constrained('inventario_psf')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('tipo_movimiento'); // cambio_ubicacion, eliminacion, nota
            $table->string('ubicacion_anterior')->nullable();
            $table->string('ubicacion_nueva')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movtosinvps');
    }
};
