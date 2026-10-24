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
        Schema::create('pemilu_prodi', function (Blueprint $table) {
            $table->foreignUuid('pemilu_id')->constrained('pemilus')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignUuid('prodi_id')->constrained('prodis')->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['pemilu_id', 'prodi_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemilu_prodi');
    }
};
