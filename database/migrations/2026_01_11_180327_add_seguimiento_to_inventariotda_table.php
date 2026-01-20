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
        Schema::table('inventariotda', function (Blueprint $table) {
            $table->enum('seguimiento', ['baja', 'garantia'])->nullable()->after('estado');
            $table->boolean('en_garantia')->default(false)->after('seguimiento');
            $table->index('seguimiento');
            $table->index('en_garantia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventariotda', function (Blueprint $table) {
            $table->dropIndex(['seguimiento']);
            $table->dropIndex(['en_garantia']);
            $table->dropColumn(['seguimiento', 'en_garantia']);
        });
    }
};
