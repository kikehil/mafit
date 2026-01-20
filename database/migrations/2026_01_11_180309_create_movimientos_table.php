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
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('tipo_movimiento', [
                'retiro',
                'remplazo_dano',
                'remplazo_renovacion',
                'agregar',
                'reingreso_garantia'
            ]);
            $table->string('cr', 50)->nullable();
            $table->string('tienda', 255)->nullable();
            $table->string('plaza', 20)->nullable();
            $table->string('nombre_plaza', 255)->nullable();
            
            // Equipo retirado/remplazado
            $table->string('equipo_retirado_placa', 100)->nullable();
            $table->string('equipo_retirado_serie', 100)->nullable();
            $table->string('equipo_retirado_descripcion', 500)->nullable();
            $table->string('equipo_retirado_marca', 100)->nullable();
            $table->string('equipo_retirado_modelo', 100)->nullable();
            $table->string('equipo_retirado_activo', 100)->nullable();
            $table->decimal('equipo_retirado_remanente', 12, 2)->nullable();
            $table->foreignId('equipo_retirado_inventariotda_id')->nullable()->constrained('inventariotda')->onDelete('set null');
            $table->foreignId('equipo_retirado_maf_id')->nullable()->constrained('maf')->onDelete('set null');
            
            // Equipo de remplazo (solo para remplazo_dano y remplazo_renovacion)
            $table->string('equipo_remplazo_placa', 100)->nullable();
            $table->string('equipo_remplazo_serie', 100)->nullable();
            $table->string('equipo_remplazo_descripcion', 500)->nullable();
            $table->string('equipo_remplazo_marca', 100)->nullable();
            $table->string('equipo_remplazo_modelo', 100)->nullable();
            $table->string('equipo_remplazo_activo', 100)->nullable();
            $table->decimal('equipo_remplazo_remanente', 12, 2)->nullable();
            $table->foreignId('equipo_remplazo_inventariotda_id')->nullable()->constrained('inventariotda')->onDelete('set null');
            $table->foreignId('equipo_remplazo_maf_id')->nullable()->constrained('maf')->onDelete('set null');
            
            // Equipo agregado (solo para agregar)
            $table->string('equipo_agregado_placa', 100)->nullable();
            $table->string('equipo_agregado_serie', 100)->nullable();
            $table->string('equipo_agregado_descripcion', 500)->nullable();
            $table->string('equipo_agregado_marca', 100)->nullable();
            $table->string('equipo_agregado_modelo', 100)->nullable();
            $table->string('equipo_agregado_activo', 100)->nullable();
            $table->decimal('equipo_agregado_remanente', 12, 2)->nullable();
            $table->foreignId('equipo_agregado_inventariotda_id')->nullable()->constrained('inventariotda')->onDelete('set null');
            
            $table->text('motivo')->nullable();
            $table->text('comentarios')->nullable();
            $table->enum('seguimiento', ['baja', 'garantia'])->nullable();
            $table->boolean('realizo_inventario')->default(false);
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['cr', 'created_at']);
            $table->index('tipo_movimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
