<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('test_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->unique(['category_id','name']);
        });

        Schema::create('test_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained('test_topics')->nullOnDelete();
            $table->enum('question_type', ['mcq','true_false'])->default('mcq');
            $table->text('question_text');
            $table->json('options')->nullable();
            $table->string('correct_answer');
            $table->text('explanation')->nullable();
            $table->enum('difficulty', ['easy','medium','hard'])->default('medium');
            $table->string('tags')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedInteger('attempt_count')->default(0);
            $table->unsignedInteger('wrong_count')->default(0);
            $table->timestamps();
            $table->index(['category_id','topic_id','difficulty','status']);
        });

        Schema::create('test_batches', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedInteger('validity_days')->default(365);
            $table->boolean('is_paid')->default(false);
            $table->enum('status', ['draft','published','archived'])->default('draft');
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->timestamps();
        });

        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_batch_id')->constrained('test_batches')->cascadeOnDelete();
            $table->string('title');
            $table->unsignedInteger('duration_minutes')->default(60);
            $table->decimal('marks_per_question', 6, 2)->default(1);
            $table->decimal('negative_mark', 6, 2)->default(0);
            $table->unsignedInteger('question_count')->default(0);
            $table->json('difficulty_distribution')->nullable();
            $table->boolean('shuffle_questions')->default(true);
            $table->boolean('shuffle_options')->default(true);
            $table->enum('status', ['draft','published','archived'])->default('draft');
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->timestamps();
        });

        Schema::create('test_question', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('tests')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('test_questions')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['test_id','question_id']);
        });

        Schema::create('test_batch_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_batch_id')->constrained('test_batches')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->enum('status', ['active','expired','revoked'])->default('active');
            $table->string('source')->default('manual');
            $table->timestamps();
            $table->unique(['test_batch_id','customer_id']);
        });

        Schema::create('test_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('tests')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->enum('status', ['in_progress','submitted','expired','abandoned'])->default('in_progress');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->unsignedInteger('time_taken_seconds')->default(0);
            $table->decimal('score', 10, 2)->default(0);
            $table->unsignedInteger('correct_count')->default(0);
            $table->unsignedInteger('wrong_count')->default(0);
            $table->unsignedInteger('skipped_count')->default(0);
            $table->unsignedInteger('rank')->nullable();
            $table->timestamps();
            $table->index(['test_id','status']);
            $table->index(['customer_id','created_at']);
        });

        Schema::create('test_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('test_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('test_questions')->cascadeOnDelete();
            $table->string('selected_answer')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('marks_awarded', 10, 2)->default(0);
            $table->unsignedInteger('time_taken_seconds')->default(0);
            $table->timestamps();
            $table->unique(['attempt_id','question_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('test_attempt_answers');
        Schema::dropIfExists('test_attempts');
        Schema::dropIfExists('test_batch_accesses');
        Schema::dropIfExists('test_question');
        Schema::dropIfExists('tests');
        Schema::dropIfExists('test_batches');
        Schema::dropIfExists('test_questions');
        Schema::dropIfExists('test_topics');
    }
};
