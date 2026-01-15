<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maf_import_batches', function (Blueprint $table) {
            $table->id();
            $table->string('period', 7); // YYYY-MM
            $table->string('filename', 255);
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->string('status', 20)->default('processing'); // processing|done|failed
            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->integer('total_rows')->default(0);
            $table->integer('inserted_rows')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('period');
            $table->index('status');
            $table->index('uploaded_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maf_import_batches');
    }
};











