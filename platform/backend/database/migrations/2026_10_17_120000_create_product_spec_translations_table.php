<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('product_spec_translations', function (Blueprint $table) {
   $table->id();
   $table->foreignId('product_spec_value_id')->constrained('product_spec_values')->cascadeOnDelete();
   $table->string('locale', 2);$table->text('value_text')->nullable();$table->timestamps();
   $table->unique(['product_spec_value_id', 'locale'], 'product_spec_translation_locale_unique');
  });
 }
 public function down(): void { Schema::dropIfExists('product_spec_translations'); }
};
