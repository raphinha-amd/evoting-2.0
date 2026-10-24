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
        Schema::table('pemilus', function (Blueprint $table) {
            $table->foreignUuid('ukm_id')->nullable()->after('password')->constrained('ukms')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemilus', function (Blueprint $table) {
            $table->dropForeign(['ukm_id']);
            $table->dropColumn('ukm_id');
        });
    }
};
