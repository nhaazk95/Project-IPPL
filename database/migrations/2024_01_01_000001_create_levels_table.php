<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration {
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('nama_level');
            $table->timestamps();
        });
 
        // Seed default levels
        \Illuminate\Support\Facades\DB::table('levels')->insert([
            ['id' => 1, 'nama_level' => 'Admin'],
            ['id' => 2, 'nama_level' => 'Kasir'],
        ]);
    }
 
    public function down(): void { Schema::dropIfExists('levels'); }
};