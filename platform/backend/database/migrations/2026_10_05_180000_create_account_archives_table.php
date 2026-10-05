<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create-only release; no existing customer/account columns are changed.
        Schema::create('account_archives', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('resource', 16);
            $table->unsignedBigInteger('target_id');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->unsignedBigInteger('actor_user_id')->index();
            $table->text('snapshot');
            $table->string('bundle_hash', 64);
            $table->string('backup_hash', 64);
            $table->string('receipt_hash', 64);
            $table->timestamp('expires_at');
            $table->timestamp('deleted_at')->nullable()->index();
            $table->timestamp('restored_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_archives');
    }
};
