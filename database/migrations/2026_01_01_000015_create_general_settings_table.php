<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            // Admin Setting
            $table->string('title')->default('Vedha');
            $table->string('favicon_path')->nullable();
            $table->string('logo_light_path')->nullable();
            $table->string('login_image_path')->nullable();
            // Storage Settings
            $table->string('storage_provider')->default('local'); // local / s3 / etc
            // Email Settings
            $table->string('mail_driver')->default('smtp');
            $table->string('mail_host')->nullable();
            $table->string('mail_port')->nullable();
            $table->string('mail_encryption')->nullable();
            $table->string('mail_username')->nullable();
            $table->string('mail_password')->nullable();
            $table->string('mail_from_address')->nullable();
            $table->string('mail_from_name')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('general_settings'); }
};
