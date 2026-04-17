<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->string('kd_order')->primary();
            $table->integer('no_meja');

            $table->string('kd_pelanggan'); // ✅ FIX

            $table->string('nama_user')->nullable();
            $table->date('tanggal');
            $table->timestamp('waktu');
            $table->text('keterangan')->nullable();
            $table->string('status_order')->default('pending');
            $table->timestamps();

            $table->foreign('no_meja')
                ->references('no_meja')->on('mejas')
                ->onDelete('cascade');

            $table->foreign('kd_pelanggan')
                ->references('kd_pelanggan')->on('pelanggans')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};