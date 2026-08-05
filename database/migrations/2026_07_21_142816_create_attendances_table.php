<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // 'siswa', 'guru'
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('class_or_subject')->nullable();
            $table->integer('lesson_hour_start')->nullable();
            $table->integer('lesson_hour_end')->nullable();
            $table->string('status');
            $table->date('date');
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};