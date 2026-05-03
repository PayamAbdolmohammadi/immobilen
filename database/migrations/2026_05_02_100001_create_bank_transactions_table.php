<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mandant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bank_import_id')->constrained('bank_imports')->cascadeOnDelete();
            $table->date('buchungsdatum');
            $table->bigInteger('betrag_cent');
            $table->string('gegenpartei')->nullable();
            $table->text('verwendungszweck')->nullable();
            $table->json('raw_row')->nullable();
            $table->string('status', 32);
            $table->timestamps();

            $table->index(['mandant_id', 'buchungsdatum']);
            $table->index(['mandant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};
