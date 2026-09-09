<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('razorpay_key_id')->nullable();
            $table->string('razorpay_key_secret')->nullable();
            $table->string('currency', 3)->default('INR');
            $table->boolean('test_mode')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('payment_settings'); }
};
