<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Single-row settings table (id = 1) — admin updates this whenever a
        // new APK is released; the app checks it on every launch and shows
        // an "Update Available" prompt if the device's version is older.
        Schema::create('app_version_settings', function (Blueprint $table) {
            $table->id();
            $table->string('latest_version')->default('1.0.0');       // e.g. "1.2.0" — shown to the user
            $table->unsignedInteger('latest_version_code')->default(1); // e.g. 2 — compared against the installed build number
            $table->string('apk_url')->nullable();                     // direct download link to the new .apk
            $table->text('release_notes')->nullable();
            $table->boolean('force_update')->default(false);          // true = user can't dismiss/skip the prompt
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('app_version_settings');
    }
};
