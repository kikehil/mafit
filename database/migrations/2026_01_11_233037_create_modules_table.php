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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Nombre único del módulo (ej: 'dashboard', 'inventario.captura')
            $table->string('display_name'); // Nombre para mostrar (ej: 'Dashboard', 'Captura Inventario')
            $table->string('route_name')->nullable(); // Nombre de la ruta asociada
            $table->string('icon')->nullable(); // Icono SVG o clase
            $table->integer('order')->default(0); // Orden de visualización
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
