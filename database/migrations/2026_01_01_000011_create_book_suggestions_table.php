<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('book_suggestions', function (Blueprint $table) {
            $table->id();
            $table->string('book_name')->nullable();
            $table->string('author_name')->nullable();
            $table->text('description')->nullable();
            $table->string('suggested_by')->nullable(); // customer name/email
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('book_suggestions'); }
};
