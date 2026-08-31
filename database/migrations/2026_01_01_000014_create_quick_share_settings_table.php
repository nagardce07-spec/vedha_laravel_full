<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('quick_share_settings', function (Blueprint $table) {
            $table->id();
            $table->string('app_scheme')->nullable();          // e.g. "vedha"
            $table->string('play_store_link')->nullable();
            $table->string('app_store_link')->nullable();
            $table->string('android_package_name')->nullable();
            $table->string('ios_bundle_id')->nullable();
            $table->string('ios_team_id')->nullable();
            $table->timestamps();
        });

        Schema::create('android_sha_keys', function (Blueprint $table) {
            $table->id();
            $table->string('sha256_key');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('android_sha_keys');
        Schema::dropIfExists('quick_share_settings');
    }
};
