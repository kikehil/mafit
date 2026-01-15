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
        Schema::create('inventariotda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maf_id')->constrained('maf')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('cr', 50)->nullable()->index();
            $table->string('tienda', 255)->nullable();
            $table->string('placa_editada', 100)->nullable();
            $table->string('marca_editada', 100)->nullable();
            $table->string('modelo_editado', 100)->nullable();
            $table->string('serie_editada', 100)->nullable();
            $table->enum('estado', ['check', 'x'])->default('check');
            $table->dateTime('fecha_inventario');
            $table->timestamps();

            $table->index(['cr', 'fecha_inventario']);
            $table->index(['user_id', 'fecha_inventario']);
            // Índice único para evitar duplicados: un equipo solo puede tener un inventario por fecha
            $table->unique(['maf_id', 'fecha_inventario']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventariotda');
    }
};
