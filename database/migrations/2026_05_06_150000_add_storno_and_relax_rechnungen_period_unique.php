<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL keeps FK on `mietvertrag_id` satisfied via the composite unique index;
        // add a plain index before dropping that unique constraint.
        Schema::table('rechnungen', function (Blueprint $table) {
            $table->index('mietvertrag_id');
        });

        Schema::table('rechnungen', function (Blueprint $table) {
            $table->dropUnique(['mietvertrag_id', 'billing_period']);
        });

        Schema::table('rechnungen', function (Blueprint $table) {
            $table->timestamp('storniert_am')->nullable()->after('bezahlt_am');
            $table->index(['mietvertrag_id', 'billing_period', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('rechnungen', function (Blueprint $table) {
            $table->dropIndex(['mietvertrag_id', 'billing_period', 'status']);
            $table->dropColumn('storniert_am');
        });

        Schema::table('rechnungen', function (Blueprint $table) {
            $table->unique(['mietvertrag_id', 'billing_period']);
        });

        Schema::table('rechnungen', function (Blueprint $table) {
            $table->dropIndex(['mietvertrag_id']);
        });
    }
};
