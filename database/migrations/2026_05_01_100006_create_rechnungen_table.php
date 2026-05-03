<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rechnungen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mandant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mieter_id')->constrained('mieter')->cascadeOnDelete();
            $table->foreignId('einheit_id')->nullable()->constrained('einheiten')->nullOnDelete();
            $table->foreignId('mietvertrag_id')->nullable()->constrained('mietvertraege')->nullOnDelete();
            $table->string('typ', 32);
            $table->char('billing_period', 7)->nullable();
            $table->unsignedBigInteger('betrag_cent');
            $table->json('source_data')->nullable();
            $table->string('status', 32);
            $table->date('faellig_am')->nullable();
            $table->timestamp('bezahlt_am')->nullable();
            $table->timestamps();

            $table->unique(['mietvertrag_id', 'billing_period']);
            $table->index(['mandant_id', 'typ', 'billing_period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rechnungen');
    }
};
