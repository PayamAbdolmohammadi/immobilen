<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mietvertraege', function (Blueprint $table) {
            $table->timestamp('sent_at')->nullable()->after('last_billed_at');
            $table->timestamp('accepted_at')->nullable()->after('sent_at');
            $table->timestamp('activated_at')->nullable()->after('accepted_at');
            $table->string('contract_pdf_path')->nullable()->after('activated_at');
            $table->string('signature_token_hash', 64)->nullable()->after('contract_pdf_path');
            $table->timestamp('token_expires_at')->nullable()->after('signature_token_hash');

            $table->index('signature_token_hash');
        });

        Schema::create('contract_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mandant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mietvertrag_id')->constrained('mietvertraege')->cascadeOnDelete();
            $table->string('event_type', 64);
            $table->string('actor_type', 32);
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['mandant_id', 'mietvertrag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_events');

        Schema::table('mietvertraege', function (Blueprint $table) {
            $table->dropIndex(['signature_token_hash']);
            $table->dropColumn([
                'sent_at',
                'accepted_at',
                'activated_at',
                'contract_pdf_path',
                'signature_token_hash',
                'token_expires_at',
            ]);
        });
    }
};
