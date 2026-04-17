<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('detail_order_temporaries', function (Blueprint $table) {
            $table->string('kd_detail')->primary();
            $table->string('order_kd')->nullable();

            $table->string('pelanggan_kd');

            $table->string('menu_kd');
            $table->integer('total');
            $table->integer('sub_total');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('menu_kd')
                ->references('kd_menu')->on('menus')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_order_temporaries');
    }
};