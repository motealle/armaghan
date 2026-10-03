<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('tracked_orders', function (Blueprint $table): void {
            $table->id();$table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->string('reference',40)->unique();$table->string('request_path',32);$table->text('description');
            $table->string('stage',32)->default('inquiry');$table->timestamp('invoice_confirmed_at')->nullable();
            $table->timestamp('deposit_confirmed_at')->nullable();$table->timestamps();$table->index(['customer_id','stage']);
        });
        Schema::create('tracked_order_events', function (Blueprint $table): void {
            $table->id();$table->foreignId('tracked_order_id')->constrained('tracked_orders')->restrictOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action',40);$table->string('stage',32);$table->text('note');$table->boolean('visible_to_customer')->default(false);$table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('tracked_order_events');Schema::dropIfExists('tracked_orders'); }
};
