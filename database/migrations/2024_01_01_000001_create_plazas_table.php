<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plazas', function (Blueprint $table) {
            $table->string('plaza', 20)->primary();
            $table->string('plaza_nom', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plazas');
    }
};

















