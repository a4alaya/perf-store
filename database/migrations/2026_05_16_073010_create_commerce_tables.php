<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('trn')->nullable();
            $table->boolean('marketing_opt_in')->default(false);
            $table->text('default_delivery_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('addresses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->default('shipping')->index();
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('emirate')->index();
            $table->string('city');
            $table->string('street_address');
            $table->string('building');
            $table->string('apartment')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('brands', function (Blueprint $table): void {
            $table->id();
            $table->json('name');
            $table->string('slug')->unique();
            $table->json('description')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('country')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->json('name');
            $table->string('slug')->unique();
            $table->json('description')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('brand_id')->constrained()->restrictOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->json('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->string('gender')->default('unisex')->index();
            $table->string('type')->default('perfume')->index();
            $table->json('short_description')->nullable();
            $table->json('description')->nullable();
            $table->json('top_notes')->nullable();
            $table->json('middle_notes')->nullable();
            $table->json('base_notes')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->integer('stock_quantity')->default(0)->index();
            $table->string('featured_image_path')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_best_seller')->default(false)->index();
            $table->boolean('is_new_arrival')->default(false)->index();
            $table->boolean('is_uae_exclusive')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('vat_taxable')->default(true);
            $table->unsignedInteger('weight_grams')->default(500);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_active', 'published_at']);
            $table->index(['brand_id', 'category_id']);
        });

        Schema::create('product_images', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->json('alt_text')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('size_label');
            $table->unsignedInteger('size_ml')->nullable()->index();
            $table->string('sku')->nullable()->unique();
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->unsignedInteger('weight_grams')->default(500);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_related', function (Blueprint $table): void {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('related_product_id')->constrained('products')->cascadeOnDelete();
            $table->primary(['product_id', 'related_product_id']);
        });

        Schema::create('coupons', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->json('name')->nullable();
            $table->string('type')->index();
            $table->decimal('value', 10, 2)->default(0);
            $table->decimal('min_order_amount', 10, 2)->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('usage_count')->default(0);
            $table->unsignedInteger('per_customer_limit')->nullable();
            $table->foreignId('customer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('carts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable()->index();
            $table->string('currency', 3)->default('AED');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('vat_total', 10, 2)->default(0);
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('cod_fee', 10, 2)->default(0);
            $table->decimal('discount_total', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->json('item_name');
            $table->timestamps();
            $table->unique(['cart_id', 'product_id', 'product_variant_id']);
        });

        Schema::create('wishlists', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'product_id']);
        });

        Schema::create('compare_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('session_id')->nullable()->index();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'product_id']);
        });

        Schema::create('shipping_zones', function (Blueprint $table): void {
            $table->id();
            $table->string('emirate')->unique();
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('free_shipping_threshold', 10, 2)->nullable();
            $table->boolean('same_day_available')->default(false);
            $table->boolean('next_day_available')->default(true);
            $table->decimal('cod_fee', 10, 2)->default(0);
            $table->decimal('min_order_amount', 10, 2)->default(0);
            $table->unsignedTinyInteger('estimated_days_min')->default(1);
            $table->unsignedTinyInteger('estimated_days_max')->default(3);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('delivery_slots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('shipping_zone_id')->nullable()->constrained()->nullOnDelete();
            $table->json('label');
            $table->time('starts_at');
            $table->time('ends_at');
            $table->time('cutoff_time')->nullable();
            $table->boolean('is_same_day')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('delivery_slot_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_number')->unique();
            $table->string('status')->default('pending_payment')->index();
            $table->string('payment_status')->default('pending')->index();
            $table->string('delivery_status')->default('not_dispatched')->index();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->string('company_trn')->nullable();
            $table->string('emirate')->index();
            $table->json('shipping_address');
            $table->json('billing_address')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('vat_total', 10, 2);
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('cod_fee', 10, 2)->default(0);
            $table->decimal('discount_total', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('currency', 3)->default('AED');
            $table->string('payment_method')->default('card');
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->date('estimated_delivery_date')->nullable();
            $table->string('idempotency_key')->nullable()->unique();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
            $table->index(['created_at', 'status']);
        });

        Schema::create('order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sku')->nullable();
            $table->json('name');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('vat_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->json('options')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('gateway')->index();
            $table->string('gateway_payment_id')->nullable()->index();
            $table->string('status')->default('pending')->index();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('AED');
            $table->string('transaction_reference')->nullable();
            $table->text('checkout_url')->nullable();
            $table->decimal('refunded_amount', 10, 2)->default(0);
            $table->json('payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('gateway')->index();
            $table->string('event_type')->nullable()->index();
            $table->string('gateway_event_id')->nullable()->unique();
            $table->string('status')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
        });

        Schema::create('refunds', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('reason')->nullable();
            $table->string('status')->default('pending')->index();
            $table->string('gateway_refund_id')->nullable()->index();
            $table->json('payload')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->string('title')->nullable();
            $table->text('body');
            $table->json('images')->nullable();
            $table->boolean('is_verified_purchase')->default(false);
            $table->string('status')->default('pending')->index();
            $table->text('admin_response')->nullable();
            $table->timestamp('reported_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('banners', function (Blueprint $table): void {
            $table->id();
            $table->json('title');
            $table->json('subtitle')->nullable();
            $table->string('image_path')->nullable();
            $table->string('link_url')->nullable();
            $table->string('location')->default('home')->index();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table): void {
            $table->id();
            $table->json('title');
            $table->string('slug')->unique();
            $table->json('body');
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table): void {
            $table->id();
            $table->string('email')->unique();
            $table->string('locale', 2)->default('en');
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table): void {
            $table->id();
            $table->string('group')->default('store')->index();
            $table->string('key');
            $table->json('value')->nullable();
            $table->string('type')->default('string');
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            $table->unique(['group', 'key']);
        });

        Schema::create('inventory_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('change');
            $table->integer('before_quantity');
            $table->integer('after_quantity');
            $table->string('reason')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('payment_logs');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('delivery_slots');
        Schema::dropIfExists('shipping_zones');
        Schema::dropIfExists('compare_items');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('product_related');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('customer_profiles');
    }
};
