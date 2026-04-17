<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->string('kd_menu')->primary();
            $table->string('name_menu');
            $table->string('kategori_id')->collation('utf8mb4_unicode_ci');
            $table->integer('harga');
            $table->text('description')->nullable();
            $table->string('status')->default('tersedia'); // tersedia / habis
            $table->string('photo')->nullable();
            $table->timestamps();
 
            $table->foreign('kategori_id')->references('kd_kategori')->on('kategoris')->onDelete('cascade');
        });
    }
 
    public function down(): void { Schema::dropIfExists('menus'); }
};