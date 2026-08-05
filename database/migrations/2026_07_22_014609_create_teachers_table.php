<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('teacher_code')->nullable()->unique(); // Kode Unik Guru
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('nip')->nullable()->unique();
            $table->string('nik')->nullable()->unique();
            $table->string('subject')->nullable();
            $table->enum('gender', ['L', 'P'])->default('L');
            $table->string('phone')->nullable();
            $table->string('religion')->nullable();
            $table->text('address')->nullable();
            $table->string('last_education')->nullable();
            $table->string('additional_task')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};