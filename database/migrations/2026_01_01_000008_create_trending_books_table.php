<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('trending_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position')->default(0); // for drag-drop ordering
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('trending_books'); }
};
