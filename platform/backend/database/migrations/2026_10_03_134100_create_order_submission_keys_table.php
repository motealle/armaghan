<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {Schema::create('order_submission_keys',function(Blueprint $table){$table->id();$table->char('scope_key',64);$table->uuid('request_key');$table->char('payload_hash',64);$table->foreignId('tracked_order_id')->nullable()->constrained('tracked_orders')->restrictOnDelete();$table->timestamps();$table->unique(['scope_key','request_key']);});}
 public function down(): void {Schema::dropIfExists('order_submission_keys');}
};
