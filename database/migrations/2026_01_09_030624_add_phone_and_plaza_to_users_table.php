<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('email');
            $table->string('plaza', 20)->nullable()->after('phone');
            
            // Foreign key constraint
            $table->foreign('plaza')
                ->references('plaza')
                ->on('plazas')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        // Cambiar el default de role a 'supervisor' usando SQL directo
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(20) DEFAULT 'supervisor'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['plaza']);
            $table->dropColumn(['phone', 'plaza']);
        });

        // Revertir el default de role a 'user'
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(20) DEFAULT 'user'");
    }
};
