<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mejas', function (Blueprint $table) {
            $table->id();
            $table->integer('no_meja')->unique();
            $table->string('user_kd')->nullable();
            $table->string('status')->default('kosong'); // kosong / terisi
            $table->timestamps();
 
            $table->foreign('user_kd')->references('kd_user')->on('users')->onDelete('set null');
        });
    }
 
    public function down(): void { Schema::dropIfExists('mejas'); }
};