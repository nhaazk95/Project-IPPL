<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kategoris', function (Blueprint $table) {
            $table->string('kd_kategori')->primary()->collation('utf8mb4_unicode_ci');
            $table->string('name_kategori');
            $table->text('description')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }
 
    public function down(): void { Schema::dropIfExists('kategoris'); }
};