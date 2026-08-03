<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('presensis', function (Blueprint $table) {
            $table->id();
            $table->string('uid_tag', 50);
            $table->foreign('uid_tag')
                ->references('uid_tag')
                ->on('siswas')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('id_kelas')
                ->nullable()
                ->constrained('kelas')
                ->nullOnDelete();
            $table->string('waktu');
            $table->string('tanggal');
            $table->enum('status', ['IN', 'OUT']);
            $table->index(['tanggal', 'status']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
