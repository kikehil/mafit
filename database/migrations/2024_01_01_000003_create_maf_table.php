<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maf', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('maf_import_batches')->onDelete('cascade');
            $table->integer('row_num'); // fila original del excel
            $table->string('plaza', 20)->nullable();
            $table->string('cr', 50)->nullable();
            $table->string('tienda', 255)->nullable();
            $table->string('placa', 100)->nullable();
            $table->string('activo', 100)->nullable();
            $table->integer('mescompra')->nullable();
            $table->integer('anocompra')->nullable();
            $table->decimal('valor_neto', 12, 2)->nullable();
            $table->decimal('remanente', 12, 2)->nullable();
            $table->string('descripcion', 500)->nullable();
            $table->string('marca', 100)->nullable();
            $table->string('modelo', 100)->nullable();
            $table->string('serie', 100)->nullable();
            $table->dateTime('imported_at');
            $table->timestamps();

            $table->index('batch_id');
            $table->index(['plaza', 'cr']);
            $table->index('cr');
            $table->index('placa');
            $table->index('activo');
            $table->index('serie');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maf');
    }
};








