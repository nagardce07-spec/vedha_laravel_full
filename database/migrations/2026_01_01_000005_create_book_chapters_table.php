<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('book_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('chapter_number');
            $table->string('title')->nullable();
            $table->enum('upload_type', ['url', 'file'])->default('file');
            $table->string('resource_path')->nullable();
            $table->string('resource_url')->nullable();
            $table->string('duration')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('book_chapters'); }
};
