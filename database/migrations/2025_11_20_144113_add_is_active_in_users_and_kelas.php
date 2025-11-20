<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->after('profile_picture')->default(false);
        });

        Schema::table('kelas', function (Blueprint $table) {
            $table->string('is_active')->after('slug')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('kelas', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
