<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // single-row settings table (id = 1 always)
        Schema::create('admob_settings', function (Blueprint $table) {
            $table->id();
            $table->string('android_banner_id')->nullable();
            $table->string('android_interstitial_id')->nullable();
            $table->string('ios_banner_id')->nullable();
            $table->string('ios_interstitial_id')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('admob_settings'); }
};
