<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_record_tags', function (Blueprint $table) {
            $table->id();
            $table->string('taggable_type');
            $table->unsignedBigInteger('taggable_id');
            $table->json('tags');
            $table->timestamps();
            $table->unique(['taggable_type', 'taggable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_record_tags');
    }
};
