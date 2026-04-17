<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('detail_orders', function (Blueprint $table) {
            $table->string('kd_detail')->primary();
            $table->string('order_kd');
            $table->string('pelanggan_kd');
            $table->string('menu_kd');
            $table->string('transaksi_kd')->nullable();
            $table->integer('total');
            $table->integer('sub_total');
            $table->text('keterangan')->nullable();
            $table->string('status_detail')->default('pending');
            $table->timestamps();

            $table->foreign('order_kd')
                ->references('kd_order')->on('orders')
                ->onDelete('cascade');

            $table->foreign('menu_kd')
                ->references('kd_menu')->on('menus')
                ->onDelete('cascade');

            $table->foreign('transaksi_kd')
                ->references('kd_transaksi')->on('transaksis')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_orders');
    }
};