<?php

use App\Enums\ProductAvailability;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->char('country_code', 2)->nullable();
            $table->string('country_name')->nullable();
            $table->string('whatsapp', 64)->nullable();
            $table->string('company_name')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedTinyInteger('priority')->default(0);
            $table->boolean('active')->default(true)->index();
            $table->boolean('direct_link_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 16)->unique();
            $table->string('name_fa');
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->string('name_ku')->nullable();
            $table->boolean('active')->default(true)->index();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('code', 16)->unique();
            $table->string('name_fa');
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->string('name_ku')->nullable();
            $table->boolean('active')->default(true)->index();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcategory_id')->constrained()->restrictOnDelete();
            $table->string('code', 64)->unique();
            $table->string('name_fa');
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->string('name_ku')->nullable();
            $table->string('availability', 32)->default(ProductAvailability::Available->value)->index();
            $table->boolean('active')->default(true)->index();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('spec_definitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcategory_id')->constrained()->cascadeOnDelete();
            $table->string('key', 64);
            $table->string('label_fa');
            $table->string('label_ar')->nullable();
            $table->string('label_en')->nullable();
            $table->string('label_ku')->nullable();
            $table->boolean('locked')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['subcategory_id', 'key']);
        });

        Schema::create('product_spec_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('spec_definition_id')->constrained()->cascadeOnDelete();
            $table->text('value_text')->nullable();
            $table->timestamps();
            $table->unique(['product_id', 'spec_definition_id']);
        });

        Schema::create('favorite_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->char('token_hash', 64)->unique();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('revoked_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('favorite_share_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('favorite_share_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['favorite_share_id', 'product_id']);
        });

        Schema::create('magic_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->char('token_hash', 64)->unique();
            $table->string('scope', 64)->default('customer_portal');
            $table->boolean('enabled')->default(true)->index();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('revoked_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 96)->index();
            $table->string('subject_type')->nullable()->index();
            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_log');
        Schema::dropIfExists('magic_links');
        Schema::dropIfExists('favorite_share_product');
        Schema::dropIfExists('favorite_shares');
        Schema::dropIfExists('product_spec_values');
        Schema::dropIfExists('spec_definitions');
        Schema::dropIfExists('products');
        Schema::dropIfExists('subcategories');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('customers');
    }
};
