<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->string('kd_transaksi')->primary();
            $table->string('order_kd');
            $table->string('user_kd');
            $table->integer('total_harga');
            $table->date('tanggal');
            $table->timestamp('waktu');
            $table->timestamps();
 
            $table->foreign('order_kd')->references('kd_order')->on('orders')->onDelete('cascade');
            $table->foreign('user_kd')->references('kd_user')->on('users')->onDelete('cascade');
        });
    }
 
    public function down(): void { Schema::dropIfExists('transaksis'); }
};