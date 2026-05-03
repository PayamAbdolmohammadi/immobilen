<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mietvertraege', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mandant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('einheit_id')->constrained('einheiten')->cascadeOnDelete();
            $table->foreignId('mieter_id')->constrained('mieter')->cascadeOnDelete();
            $table->date('starts_on');
            $table->date('ends_on')->nullable();
            $table->string('status', 32)->index();
            $table->unsignedBigInteger('kaltmiete_cent');
            $table->unsignedBigInteger('nebenkosten_vorauszahlung_cent');
            $table->string('zahlungsintervall', 32)->default('monthly');
            $table->unsignedTinyInteger('faelligkeit_tag')->nullable();
            $table->char('next_billing_period', 7)->nullable();
            $table->timestamp('last_billed_at')->nullable();
            $table->timestamps();

            $table->index(['mandant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mietvertraege');
    }
};
