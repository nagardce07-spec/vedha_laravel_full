<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('theme_color')->default('#7b2cbf');
            $table->string('theme_light_color')->default('#E0AAFF');
            $table->string('theme_background_color')->default('#0D0D0D');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('app_settings'); }
};
