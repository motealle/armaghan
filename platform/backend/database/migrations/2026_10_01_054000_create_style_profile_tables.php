<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('style_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 64)->unique();
            $table->string('name', 120);
            $table->unsignedSmallInteger('schema_version')->default(1);
            $table->json('draft_styles');
            $table->json('draft_texts');
            $table->longText('draft_css');
            $table->char('draft_checksum', 64)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('style_profile_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('style_profile_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->unsignedSmallInteger('schema_version')->default(1);
            $table->string('source_test', 16)->nullable();
            $table->json('styles');
            $table->json('texts');
            $table->longText('compiled_css');
            $table->char('checksum', 64)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['style_profile_id', 'version']);
        });

        Schema::create('style_profile_publications', function (Blueprint $table) {
            $table->id();
            $table->string('channel', 24)->unique();
            $table->foreignId('style_profile_version_id')->constrained()->restrictOnDelete();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('style_profile_publications');
        Schema::dropIfExists('style_profile_versions');
        Schema::dropIfExists('style_profiles');
    }
};
