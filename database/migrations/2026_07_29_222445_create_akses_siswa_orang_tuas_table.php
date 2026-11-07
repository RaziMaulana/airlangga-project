<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('akses_siswa_orang_tuas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orangtua_id')
                ->constrained('orang_tuas')
                ->cascadeOnDelete();
            $table->string('id_siswa', 36);
            $table->foreign('id_siswa')
                ->references('id_siswa')
                ->on('siswas')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['orangtua_id', 'id_siswa']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akses_siswa_orang_tuas');
    }
};
