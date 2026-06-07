<?php

namespace Database\Seeders;

use App\Models\DeliverySlot;
use App\Models\Page;
use App\Models\Setting;
use App\Models\ShippingZone;
use App\Models\StoreSection;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $delivery = [
            'abu_dhabi' => [25, 650, false, 2, 3],
            'dubai' => [20, 500, true, 1, 2],
            'sharjah' => [22, 500, true, 1, 2],
            'ajman' => [25, 550, false, 2, 3],
            'umm_al_quwain' => [30, 650, false, 2, 4],
            'ras_al_khaimah' => [35, 700, false, 2, 4],
            'fujairah' => [35, 700, false, 2, 4],
        ];

        foreach ($delivery as $emirate => [$fee, $threshold, $sameDay, $min, $max]) {
            $zone = ShippingZone::updateOrCreate(
                ['emirate' => $emirate],
                [
                    'delivery_fee' => $fee,
                    'free_shipping_threshold' => $threshold,
                    'same_day_available' => $sameDay,
                    'next_day_available' => true,
                    'cod_fee' => 10,
                    'min_order_amount' => 100,
                    'estimated_days_min' => $min,
                    'estimated_days_max' => $max,
                    'is_active' => true,
                ],
            );

            foreach ([
                [['en' => 'Morning delivery', 'ar' => 'توصيل صباحي'], '09:00', '12:00', false],
                [['en' => 'Afternoon delivery', 'ar' => 'توصيل بعد الظهر'], '13:00', '17:00', false],
                [['en' => 'Evening delivery', 'ar' => 'توصيل مسائي'], '18:00', '21:00', $sameDay],
            ] as [$label, $start, $end, $same]) {
                DeliverySlot::updateOrCreate(
                    ['shipping_zone_id' => $zone->id, 'starts_at' => $start, 'ends_at' => $end],
                    ['label' => $label, 'cutoff_time' => '14:00', 'is_same_day' => $same, 'is_active' => true],
                );
            }
        }

        foreach ([
            'vat' => ['rate' => config('store.vat_rate'), 'trn' => config('store.trn')],
            'payments' => ['gateway' => config('store.payment_gateway'), 'cod_enabled' => true],
            'brand' => ['primary' => '#1a1a1a', 'gold' => '#e9b26d', 'copper' => '#ab7235', 'mist' => '#e8e8e8'],
        ] as $group => $values) {
            foreach ($values as $key => $value) {
                Setting::updateOrCreate(['group' => $group, 'key' => $key], ['value' => ['value' => $value], 'is_public' => true]);
            }
        }

        $pages = [
            'about' => [
                'en' => 'Maison De Mystere is a luxury niche perfume boutique built on more than 20 years of fragrance, premium retail, and brand development expertise. We curate meaningful olfactory experiences that embody quiet luxury and personal expression.',
                'ar' => 'ميزون دي ميستير بوتيك فاخر للعطور النيش مبني على أكثر من 20 عاما من الخبرة في العطور والتجزئة الفاخرة وتطوير العلامات التجارية. نحن ننسق تجارب عطرية راقية تعبر عن الفخامة الهادئة والهوية الشخصية.',
            ],
            'faq' => [
                'en' => 'Delivery is available across all seven emirates. VAT is calculated at 5%. Online card payment and cash on delivery can be enabled by the store administrator.',
                'ar' => 'التوصيل متاح في جميع الإمارات السبع. يتم احتساب ضريبة القيمة المضافة بنسبة 5%. يمكن تفعيل الدفع الإلكتروني والدفع عند الاستلام من لوحة الإدارة.',
            ],
            'privacy-policy' => [
                'en' => 'We collect only the customer, delivery, and payment information required to process UAE orders securely. Payment card data is handled by the configured payment gateway.',
                'ar' => 'نجمع فقط بيانات العميل والتوصيل والدفع المطلوبة لمعالجة طلبات الإمارات بأمان. تتم معالجة بيانات البطاقات عبر بوابة الدفع المفعلة.',
            ],
            'terms-and-conditions' => [
                'en' => 'Orders are subject to stock availability, payment approval, UAE delivery coverage, and the policies displayed at checkout.',
                'ar' => 'تخضع الطلبات لتوفر المخزون والموافقة على الدفع ونطاق التوصيل داخل الإمارات والسياسات المعروضة عند الدفع.',
            ],
            'return-and-refund-policy' => [
                'en' => 'Returns are reviewed according to product condition, hygiene requirements, and UAE consumer expectations. Approved refunds are processed through the original payment method.',
                'ar' => 'تتم مراجعة المرتجعات حسب حالة المنتج ومتطلبات السلامة وتوقعات المستهلك في الإمارات. تتم معالجة المبالغ المستردة المقبولة عبر وسيلة الدفع الأصلية.',
            ],
        ];

        foreach ($pages as $slug => $body) {
            Page::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => ['en' => str($slug)->replace('-', ' ')->title()->toString(), 'ar' => $this->arabicTitle($slug)],
                    'body' => $body,
                    'meta_title' => [
                        'en' => str($slug)->replace('-', ' ')->title()->append(' | Maison De Mystere')->toString(),
                        'ar' => $this->arabicTitle($slug).' | ميزون دي ميستير',
                    ],
                    'meta_description' => [
                        'en' => str($body['en'])->limit(150)->toString(),
                        'ar' => str($body['ar'])->limit(150)->toString(),
                    ],
                    'is_active' => true,
                ],
            );
        }

        $this->seedStoreSections();
    }

    private function seedStoreSections(): void
    {
        $sections = [
            [
                'key' => 'home.hero',
                'type' => 'hero',
                'title' => ['en' => 'Maison De Mystere', 'ar' => 'ميزون دي ميستير'],
                'subtitle' => [
                    'en' => 'A refined UAE destination for rare niche perfumes, luxury oud, bakhoor, attars, and unforgettable personal expression.',
                    'ar' => 'وجهة إماراتية راقية للعطور النادرة والعود الفاخر والبخور والعطور الزيتية وتجارب عطرية لا تنسى.',
                ],
                'image_path' => 'images/products/perfume-1.jpeg',
                'secondary_image_path' => 'images/products/perfume-2.jpeg',
                'cta_label' => ['en' => 'Shop Perfumes', 'ar' => 'تسوق العطور'],
                'cta_url' => '/products',
                'secondary_cta_label' => ['en' => 'Discover Oud', 'ar' => 'اكتشف العود'],
                'secondary_cta_url' => '/products?type=oud',
                'background_style' => 'light',
                'limit' => 1,
                'sort_order' => 10,
            ],
            [
                'key' => 'home.features',
                'type' => 'feature_strip',
                'background_style' => 'default',
                'limit' => 3,
                'sort_order' => 20,
                'items' => [
                    ['truck', ['en' => 'Same-day and next-day delivery options by emirate', 'ar' => 'خيارات توصيل في نفس اليوم أو اليوم التالي حسب الإمارة']],
                    ['shield-check', ['en' => 'Secure payment with Stripe UAE, Tap-ready abstraction, and COD option', 'ar' => 'دفع آمن عبر Stripe UAE مع طبقة جاهزة لـ Tap وخيار الدفع عند الاستلام']],
                    ['receipt-percent', ['en' => 'AED pricing with 5% UAE VAT invoice breakdown', 'ar' => 'أسعار بالدرهم مع تفصيل ضريبة القيمة المضافة الإماراتية 5%']],
                ],
            ],
            [
                'key' => 'home.featured',
                'type' => 'product_rail',
                'title' => ['en' => 'Featured Perfumes', 'ar' => 'عطور مختارة'],
                'product_source' => 'featured',
                'limit' => 8,
                'sort_order' => 30,
            ],
            [
                'key' => 'home.oud',
                'type' => 'split_product_feature',
                'title' => ['en' => 'Luxury Oud', 'ar' => 'العود الفاخر'],
                'subtitle' => [
                    'en' => 'Deep woods, resinous warmth, and Gulf elegance curated for collectors who prefer quiet intensity over excess.',
                    'ar' => 'أخشاب عميقة ودفء راتنجي وأناقة خليجية مختارة لعشاق العطور الهادئة ذات الحضور العميق.',
                ],
                'cta_label' => ['en' => 'Shop Oud', 'ar' => 'تسوق العود'],
                'cta_url' => '/products?type=oud',
                'product_source' => 'oud',
                'background_style' => 'soft',
                'limit' => 2,
                'sort_order' => 40,
            ],
            [
                'key' => 'home.best_sellers',
                'type' => 'product_rail',
                'title' => ['en' => 'Best Sellers', 'ar' => 'الأكثر مبيعا'],
                'product_source' => 'best_sellers',
                'limit' => 8,
                'sort_order' => 50,
            ],
            [
                'key' => 'home.new_arrivals',
                'type' => 'product_rail',
                'title' => ['en' => 'New Arrivals', 'ar' => 'وصل حديثا'],
                'product_source' => 'new_arrivals',
                'limit' => 8,
                'sort_order' => 60,
            ],
            [
                'key' => 'home.uae_exclusive',
                'type' => 'product_rail',
                'title' => ['en' => 'UAE Exclusive Collection', 'ar' => 'مجموعة حصرية للإمارات'],
                'subtitle' => [
                    'en' => 'Controlled quantities, boutique-first launches, and fragrances selected for Gulf and European taste.',
                    'ar' => 'كميات محدودة وإطلاقات حصرية للمتجر وروائح منتقاة للذائقة الخليجية والأوروبية.',
                ],
                'cta_label' => ['en' => 'View Collection', 'ar' => 'عرض المجموعة'],
                'cta_url' => '/products?uae=exclusive',
                'product_source' => 'uae_exclusive',
                'background_style' => 'soft',
                'limit' => 8,
                'sort_order' => 70,
            ],
            [
                'key' => 'home.categories',
                'type' => 'taxonomy_grid',
                'title' => ['en' => 'Shop by Category', 'ar' => 'تسوق حسب الفئة'],
                'taxonomy_source' => 'categories',
                'limit' => 8,
                'sort_order' => 80,
            ],
            [
                'key' => 'home.brands',
                'type' => 'taxonomy_grid',
                'title' => ['en' => 'Shop by Brand', 'ar' => 'تسوق حسب العلامة'],
                'taxonomy_source' => 'brands',
                'limit' => 10,
                'sort_order' => 90,
            ],
            [
                'key' => 'home.gift_sets',
                'type' => 'product_rail',
                'title' => ['en' => 'Gift Sets', 'ar' => 'مجموعات الهدايا'],
                'product_source' => 'gift_sets',
                'limit' => 8,
                'sort_order' => 100,
            ],
            [
                'key' => 'home.reviews',
                'type' => 'review_grid',
                'title' => ['en' => 'Customer Reviews', 'ar' => 'آراء العملاء'],
                'limit' => 6,
                'sort_order' => 110,
            ],
        ];

        foreach ($sections as $sectionData) {
            $items = $sectionData['items'] ?? [];
            unset($sectionData['items']);

            $section = StoreSection::updateOrCreate(
                ['key' => $sectionData['key']],
                $sectionData + ['is_active' => true],
            );

            foreach ($items as $index => [$icon, $title]) {
                $section->items()->updateOrCreate(
                    ['sort_order' => ($index + 1) * 10],
                    [
                        'title' => $title,
                        'icon' => $icon,
                        'is_active' => true,
                    ],
                );
            }
        }
    }

    private function arabicTitle(string $slug): string
    {
        return [
            'about' => 'من نحن',
            'faq' => 'الأسئلة الشائعة',
            'privacy-policy' => 'سياسة الخصوصية',
            'terms-and-conditions' => 'الشروط والأحكام',
            'return-and-refund-policy' => 'سياسة الاسترجاع والاسترداد',
        ][$slug] ?? $slug;
    }
}
