<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 32)->default('owner')->after('mandant_id');
        });

        Schema::table('bank_imports', function (Blueprint $table) {
            $table->string('csv_profile', 32)->nullable()->after('original_filename');
            $table->text('error_message')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('bank_imports', function (Blueprint $table) {
            $table->dropColumn(['csv_profile', 'error_message']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
