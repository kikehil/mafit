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
            $table->string('foto1', 255)->nullable()->after('serie_editada');
            $table->string('foto2', 255)->nullable()->after('foto1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventariotda', function (Blueprint $table) {
            $table->dropColumn(['foto1', 'foto2']);
        });
    }
};
