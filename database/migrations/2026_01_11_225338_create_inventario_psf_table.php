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
        Schema::create('inventario_psf', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('maf_id')->nullable();
            $table->foreign('maf_id')->references('id')->on('maf')->onDelete('set null');
            $table->string('plaza')->nullable();
            $table->string('nombre_plaza')->nullable();
            $table->string('cr')->nullable();
            $table->string('nombre_tienda')->nullable();
            $table->string('placa')->nullable();
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('serie')->nullable();
            $table->string('activo')->nullable();
            $table->integer('anocompra')->nullable();
            $table->decimal('valor_neto', 15, 2)->nullable();
            $table->decimal('remanente', 15, 2)->nullable();
            $table->string('ubicacion')->nullable();
            $table->string('plaza_usuario')->nullable(); // Tampico, Valles, Matamoros, Victoria
            $table->text('notas')->nullable();
            $table->boolean('encontrado_en_maf')->default(false);
            $table->boolean('activo_registro')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario_psf');
    }
};
