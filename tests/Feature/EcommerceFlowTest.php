<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EcommerceFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_browse_products_and_view_detail_page(): void
    {
        $this->seed();

        $product = Product::query()->firstOrFail();

        $this->get(route('products.index', ['locale' => 'en']))
            ->assertOk()
            ->assertSee('Shop Perfumes')
            ->assertSee($product->localized('name'));

        $this->get(route('products.show', ['locale' => 'en', 'product' => $product]))
            ->assertOk()
            ->assertSee($product->localized('name'))
            ->assertSee('AED')
            ->assertDontSee('product_variant_id', false)
            ->assertDontSee('Size');
    }

    public function test_cart_checkout_calculates_uae_vat_and_free_dubai_shipping(): void
    {
        Notification::fake();
        $this->seed();

        $product = Product::query()->where('price', '>', 1000)->firstOrFail();

        $this->post(route('cart.store', ['locale' => 'en']), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertRedirect();

        $this->post(route('checkout.store', ['locale' => 'en']), [
            'full_name' => 'Amina Saleh',
            'email' => 'amina@example.com',
            'phone' => '+971501234567',
            'emirate' => 'dubai',
            'city' => 'Downtown Dubai',
            'street_address' => 'Sheikh Mohammed bin Rashid Boulevard',
            'building' => 'Villa 8',
            'apartment' => null,
            'payment_method' => 'card',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $order = Order::query()->latest()->firstOrFail();

        $this->assertSame('AED', $order->currency);
        $this->assertEquals(0.0, (float) $order->shipping_fee);
        $this->assertGreaterThan(0, (float) $order->vat_total);
        $this->assertEquals(round(((float) $order->subtotal - (float) $order->discount_total) * 0.05, 2), (float) $order->vat_total);
    }

    public function test_payment_webhook_marks_order_paid_once(): void
    {
        Notification::fake();
        $this->seed();

        $product = Product::query()->firstOrFail();
        $this->post(route('cart.store', ['locale' => 'en']), ['product_id' => $product->id, 'quantity' => 1]);
        $this->post(route('checkout.store', ['locale' => 'en']), [
            'full_name' => 'Omar Khalid',
            'email' => 'omar@example.com',
            'phone' => '+971551234567',
            'emirate' => 'dubai',
            'city' => 'Dubai Marina',
            'street_address' => 'Marina Walk',
            'building' => 'Tower 2',
            'payment_method' => 'card',
        ])->assertSessionHasNoErrors();

        $order = Order::query()->latest()->firstOrFail();
        $payload = [
            'id' => 'evt_test_paid',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_paid',
                    'payment_status' => 'paid',
                    'payment_intent' => 'pi_test_paid',
                    'metadata' => ['order_id' => $order->id, 'order_number' => $order->order_number],
                ],
            ],
        ];

        $this->postJson(route('payments.webhook'), $payload)->assertOk();
        $this->postJson(route('payments.webhook'), $payload)->assertOk();

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'payment_status' => 'paid']);
        $this->assertDatabaseCount('payment_logs', 1);
    }

    public function test_admin_can_access_filament_panel(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@maisondemystere.ae')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Dashboard');
    }

    public function test_admin_can_access_dynamic_store_section_resources(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@maisondemystere.ae')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin/store-sections')
            ->assertOk()
            ->assertSee('Store Sections');

        $this->actingAs($admin)
            ->get('/admin/store-section-items')
            ->assertOk()
            ->assertSee('Section Items');
    }

    public function test_admin_can_view_a_product_record_page(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@maisondemystere.ae')->firstOrFail();
        $product = Product::query()->with(['brand', 'category'])->firstOrFail();

        $this->actingAs($admin)
            ->get("/admin/products/{$product->id}")
            ->assertOk()
            ->assertSee($product->localized('name'))
            ->assertSee($product->brand->localized('name'))
            ->assertSee($product->category->localized('name'));
    }

    public function test_admin_can_view_a_brand_record_page(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@maisondemystere.ae')->firstOrFail();
        $brand = Brand::query()->firstOrFail();

        $this->actingAs($admin)
            ->get("/admin/brands/{$brand->id}")
            ->assertOk()
            ->assertSee($brand->localized('name'));
    }

    public function test_admin_can_view_a_category_record_page(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@maisondemystere.ae')->firstOrFail();
        $category = Category::query()->firstOrFail();

        $this->actingAs($admin)
            ->get("/admin/categories/{$category->id}")
            ->assertOk()
            ->assertSee($category->localized('name'));
    }
}
