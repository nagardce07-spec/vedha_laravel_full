<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('test_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('test_questions', 'question_id')) {
                $table->string('question_id')->nullable()->unique();
            }
            if (!Schema::hasColumn('test_questions', 'category')) {
                $table->string('category')->nullable()->index();
            }
            if (!Schema::hasColumn('test_questions', 'topic')) {
                $table->string('topic')->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('test_questions', function (Blueprint $table) {
            if (Schema::hasColumn('test_questions', 'question_id')) {
                $table->dropUnique(['question_id']);
                $table->dropColumn('question_id');
            }
            if (Schema::hasColumn('test_questions', 'category')) {
                $table->dropIndex(['category']);
                $table->dropColumn('category');
            }
            if (Schema::hasColumn('test_questions', 'topic')) {
                $table->dropIndex(['topic']);
                $table->dropColumn('topic');
            }
        });
    }
};
