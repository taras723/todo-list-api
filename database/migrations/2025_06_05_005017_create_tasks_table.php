<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Create the tasks table
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('tasks')->onDelete('cascade');
            $table->enum('status', ['todo', 'done'])->default('todo');
            $table->unsignedTinyInteger('priority')->check('priority BETWEEN 1 AND 5');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->timestamp('completed_at')->nullable();
            $table->index('status');
            $table->index('priority');
            $table->index('user_id');
            $table->fullText(['title', 'description']);
        });
    }

    // Drop the tasks table
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};