<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('has_class')->default(false);
            $table->boolean('can_vote')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Ubah kolom role di users dari enum menjadi VARCHAR(50) agar fleksibel mengikuti role dinamis
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'mahasiswa'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'siswa', 'guru', 'caraka') NOT NULL DEFAULT 'siswa'");
    }
};
