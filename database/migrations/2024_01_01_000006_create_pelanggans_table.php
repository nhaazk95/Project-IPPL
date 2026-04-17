<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pelanggans', function (Blueprint $table) {
            $table->string('kd_pelanggan')->primary();
            $table->string('name_pelanggan');
            $table->integer('no_meja');
            $table->timestamps();
 
            $table->foreign('no_meja')->references('no_meja')->on('mejas')->onDelete('cascade');
        });
    }
 
    public function down(): void { Schema::dropIfExists('pelanggans'); }
};