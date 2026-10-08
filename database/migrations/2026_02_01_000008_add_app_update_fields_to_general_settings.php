<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->string('latest_version')->nullable();     // e.g. "1.2.0"
            $table->string('apk_url')->nullable();             // direct download link to the new APK
            $table->boolean('force_update')->default(false);   // true = block app until updated
            $table->text('update_notes')->nullable();          // "What's new" shown in the dialog
        });
    }
    public function down(): void {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn(['latest_version', 'apk_url', 'force_update', 'update_notes']);
        });
    }
};
