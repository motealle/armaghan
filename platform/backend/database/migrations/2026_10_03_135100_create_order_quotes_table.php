<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {Schema::create('order_quotes',function(Blueprint $table){$table->id();$table->foreignId('tracked_order_id')->unique()->constrained('tracked_orders')->restrictOnDelete();$table->string('currency',3);$table->json('items');$table->unsignedBigInteger('subtotal_minor');$table->unsignedBigInteger('shipping_minor')->default(0);$table->unsignedBigInteger('discount_minor')->default(0);$table->unsignedBigInteger('tax_minor')->default(0);$table->unsignedBigInteger('total_minor');$table->timestamps();});}
 public function down(): void {Schema::dropIfExists('order_quotes');}
};
