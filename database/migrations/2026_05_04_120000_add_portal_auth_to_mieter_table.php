<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mieter', function (Blueprint $table) {
            $table->string('password')->nullable()->after('email');
            $table->rememberToken();
        });

        Schema::table('mieter', function (Blueprint $table) {
            $table->unique('email');
        });
    }

    public function down(): void
    {
        Schema::table('mieter', function (Blueprint $table) {
            $table->dropUnique(['email']);
        });

        Schema::table('mieter', function (Blueprint $table) {
            $table->dropColumn(['password', 'remember_token']);
        });
    }
};
