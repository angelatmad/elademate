<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 120);
            $table->text('description')->nullable();
            $table->string('category', 50)->nullable();
            $table->string('priority', 20)->default('medium');
            $table->string('status', 20)->default('pending');
            $table->date('due_date')->nullable();
            $table->time('due_time')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'due_date']);
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
