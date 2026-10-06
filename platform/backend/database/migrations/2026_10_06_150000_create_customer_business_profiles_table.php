<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_business_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->unique()->constrained()->restrictOnDelete();
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('phone', 64)->nullable();
            $table->string('preferred_language', 8)->nullable();
            foreach (['city', 'district', 'shop_number', 'sales_product_group', 'purchase_volume',
                'cooperation_type', 'sales_type'] as $field) {
                $table->string($field)->nullable();
            }
            $table->boolean('pinned')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_business_profiles');
    }
};
