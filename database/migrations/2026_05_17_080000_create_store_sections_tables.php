<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_sections', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('type')->index();
            $table->json('title')->nullable();
            $table->json('subtitle')->nullable();
            $table->json('eyebrow')->nullable();
            $table->json('body')->nullable();
            $table->string('image_path')->nullable();
            $table->string('secondary_image_path')->nullable();
            $table->json('cta_label')->nullable();
            $table->string('cta_url')->nullable();
            $table->json('secondary_cta_label')->nullable();
            $table->string('secondary_cta_url')->nullable();
            $table->string('product_source')->nullable()->index();
            $table->string('taxonomy_source')->nullable()->index();
            $table->string('background_style')->default('default');
            $table->unsignedTinyInteger('limit')->default(8);
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('store_section_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('store_section_id')->constrained()->cascadeOnDelete();
            $table->json('title');
            $table->json('subtitle')->nullable();
            $table->string('icon')->nullable();
            $table->string('image_path')->nullable();
            $table->string('link_url')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_section_items');
        Schema::dropIfExists('store_sections');
    }
};
