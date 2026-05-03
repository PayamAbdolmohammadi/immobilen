<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zahlungszuordnungen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mandant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bank_transaction_id')->constrained('bank_transactions')->cascadeOnDelete();
            $table->foreignId('rechnung_id')->constrained('rechnungen')->cascadeOnDelete();
            $table->unsignedBigInteger('betrag_cent');
            $table->timestamps();

            $table->index(['bank_transaction_id', 'rechnung_id']);
            $table->index(['mandant_id', 'rechnung_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zahlungszuordnungen');
    }
};
