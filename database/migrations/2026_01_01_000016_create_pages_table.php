<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // "privacy-policy" / "terms-of-uses"
            $table->string('title');
            $table->longText('content')->nullable(); // rich text HTML from the WYSIWYG editor
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('pages'); }
};
