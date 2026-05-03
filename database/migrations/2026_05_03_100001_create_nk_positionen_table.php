<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nk_positionen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nk_abrechnung_id')->constrained('nk_abrechnungen')->cascadeOnDelete();
            $table->string('beschreibung');
            $table->unsignedBigInteger('betrag_cent');
            $table->timestamps();

            $table->index('nk_abrechnung_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nk_positionen');
    }
};
