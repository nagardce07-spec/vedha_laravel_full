<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->unsignedInteger('latest_version_code')->default(1); // matches Flutter's build number
            $table->string('latest_version_name')->default('1.0.0');   // display version, e.g. "1.2.0"
            $table->string('update_url')->nullable();                  // direct APK download link
            $table->text('update_message')->nullable();                // "What's new" text shown in the dialog
            $table->boolean('force_update')->default(false);           // true = block app usage until updated
        });
    }
    public function down(): void {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn(['latest_version_code', 'latest_version_name', 'update_url', 'update_message', 'force_update']);
        });
    }
};
