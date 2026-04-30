<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

			$table->string('title');
			$table->text('description')->nullable();
			$table->enum('status', ['todo', 'in_progress', 'completed'])->default('todo');
			$table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
			$table->date('due_date')->nullable();
			$table->timestamp('completed_at')->nullable();
			$table->softDeletes();

			// Foreign relationship
			$table->foreignId('user_id')->constrained()->onDelete('cascade');
			$table->foreignId('project_id')->constrained()->onDelete('cascade');

			// Index for performance
			$table->index('user_id');
			$table->index('project_id');
			$table->index('status');
			$table->index('priority');
			$table->index('due_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
