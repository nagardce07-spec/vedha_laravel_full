<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('playlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('playlist_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playlist_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['playlist_id', 'book_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('playlist_books');
        Schema::dropIfExists('playlists');
    }
};
