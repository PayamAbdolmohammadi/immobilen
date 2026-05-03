<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mandant_id')->constrained()->cascadeOnDelete();
            $table->string('original_filename');
            $table->unsignedInteger('row_count')->default(0);
            $table->string('status', 32);
            $table->timestamps();

            $table->index(['mandant_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_imports');
    }
};
