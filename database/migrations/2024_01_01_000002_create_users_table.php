<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('kd_user')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('password');
            $table->unsignedBigInteger('level_id');
            $table->rememberToken();
            $table->timestamps();
 
            $table->foreign('level_id')->references('id')->on('levels')->onDelete('cascade');
        });
    }
 
    public function down(): void { Schema::dropIfExists('users'); }
};