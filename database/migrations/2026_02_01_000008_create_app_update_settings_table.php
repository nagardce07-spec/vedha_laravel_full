<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Single-row settings table (id = 1) — admin sets this whenever a
        // new APK is released, so the app can prompt users to update.
        Schema::create('app_update_settings', function (Blueprint $table) {
            $table->id();
            $table->string('latest_version')->default('1.0.0');   // shown to users, e.g. "1.2.0"
            $table->unsignedInteger('latest_version_code')->default(1); // compared numerically against the installed build number
            $table->string('apk_url')->nullable();     // direct download link for the new APK
            $table->text('release_notes')->nullable(); // "What's new" shown in the popup
            $table->boolean('force_update')->default(false); // true = block app usage until updated
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('app_update_settings');
    }
};
