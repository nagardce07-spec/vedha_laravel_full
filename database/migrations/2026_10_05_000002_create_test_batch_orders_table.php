<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('test_batch_orders', function(Blueprint $table){$table->id();$table->foreignId('test_batch_id')->constrained('test_batches')->cascadeOnDelete();$table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();$table->string('razorpay_order_id')->nullable()->index();$table->string('razorpay_payment_id')->nullable();$table->string('razorpay_signature')->nullable();$table->decimal('amount',10,2);$table->string('currency',10)->default('INR');$table->enum('status',['pending','paid','failed'])->default('pending');$table->dateTime('starts_at')->nullable();$table->dateTime('expires_at')->nullable();$table->timestamps();}); }
 public function down(): void {Schema::dropIfExists('test_batch_orders');}
};
