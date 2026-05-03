<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('einheiten', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mandant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('objekt_id')->constrained('objekte')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();

            $table->index(['mandant_id', 'objekt_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('einheiten');
    }
};
