<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nk_abrechnungen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mandant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('objekt_id')->constrained('objekte')->cascadeOnDelete();
            $table->unsignedSmallInteger('jahr');
            $table->string('verteilungs_art', 64)->default('gleich_pro_einheit');
            $table->string('status', 32)->default('draft');
            $table->timestamps();

            $table->unique(['objekt_id', 'jahr']);
            $table->index(['mandant_id', 'jahr']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nk_abrechnungen');
    }
};
