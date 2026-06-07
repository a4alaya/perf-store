<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            ['Luxury Perfumes', 'العطور الفاخرة', 'perfume'],
            ['Arabic Perfumes', 'العطور العربية', 'perfume'],
            ['Oud', 'العود', 'oud'],
            ['Bakhoor', 'البخور', 'bakhoor'],
            ['Attars', 'العطور الزيتية', 'attar'],
            ['Gift Sets', 'مجموعات الهدايا', 'gift_set'],
            ['Limited Editions', 'إصدارات محدودة', 'perfume'],
        ])->mapWithKeys(function (array $row) {
            $category = Category::updateOrCreate(
                ['slug' => Str::slug($row[0])],
                [
                    'name' => ['en' => $row[0], 'ar' => $row[1]],
                    'description' => [
                        'en' => "Curated {$row[0]} for the UAE fragrance market.",
                        'ar' => $this->categoryDescriptionArabic($row[1]),
                    ],
                    'is_active' => true,
                    'sort_order' => Category::count() + 1,
                ],
            );

            return [$row[2] => $category];
        });

        $rows = [
            ['ARMANI', 'Oud Royal 100 ML', 'AR1001', 1445, 'oud', 'unisex'],
            ['ARMANI', 'Rose d Arabie 100 ML', 'AR1002', 1445, 'perfume', 'unisex'],
            ['ARMANI', 'Bleu Lazuli 100 ML', 'AR1003', 1445, 'perfume', 'men'],
            ['ARMANI', 'Vert Malachite 100 ML', 'AR1004', 1000, 'perfume', 'women'],
            ['Louis Vuitton', 'Ombre Nomade 100 ML', 'LV1006', 1770, 'oud', 'unisex'],
            ['Louis Vuitton', 'Imagination 100 ML', 'LV1007', 1400, 'perfume', 'men'],
            ['Louis Vuitton', 'Attrape-Reves 100 ML', 'LV1008', 1400, 'perfume', 'women'],
            ['Gucci', 'The Heart of Leo 100 ML', 'GU1011', 1450, 'perfume', 'unisex'],
            ['Gucci', 'Tears From The Moon 100 ML', 'GU1013', 1450, 'perfume', 'women'],
            ['Bvlgari', 'Tygar 125 ML', 'BLG1016', 1793, 'perfume', 'men'],
            ['AMOUAGE', 'Guidance EDP 100 ML', 'AMG01', 1550, 'perfume', 'women'],
            ['AMOUAGE', 'Decision EDP 100 ML', 'AMG02', 1550, 'perfume', 'men'],
            ['AMOUAGE', 'Purpose 50 EDP 100 ML', 'AMG03', 2150, 'perfume', 'unisex'],
            ['ATELIER DES ORS', 'Noir By Night EDP 100 ML', 'ADO02', 1150, 'perfume', 'unisex'],
            ['ATELIER DES ORS', 'Rose Omeyyade Extrait 100 ML', 'ADO04', 1150, 'perfume', 'women'],
            ['Kilian', 'Love Don\'t Be Shy 50 ML', 'KN01', 1100, 'perfume', 'women'],
            ['Kilian', 'Angels Share 100 ML', 'KN02', 1605, 'perfume', 'unisex'],
            ['Maison De Mystere', 'Mystere Oud Signature 75 ML', 'MDM01', 950, 'oud', 'unisex'],
            ['Maison De Mystere', 'Bakhoor Noir Gift Set', 'MDM02', 420, 'gift_set', 'unisex'],
            ['Maison De Mystere', 'Golden Attar Discovery Set', 'MDM03', 320, 'attar', 'unisex'],
        ];

        foreach ($rows as $index => [$brandName, $productName, $sku, $price, $type, $gender]) {
            $brand = Brand::updateOrCreate(
                ['slug' => Str::slug($brandName)],
                [
                    'name' => ['en' => trim($brandName), 'ar' => $brandName === 'Maison De Mystere' ? 'ميزون دي ميستير' : trim($brandName)],
                    'description' => [
                        'en' => "Selected {$brandName} fragrances curated for Maison De Mystere clients.",
                        'ar' => $this->brandDescriptionArabic($brandName),
                    ],
                    'is_active' => true,
                ],
            );

            $category = $categories[$type] ?? $categories['perfume'];
            $product = Product::updateOrCreate(
                ['sku' => $sku],
                [
                    'brand_id' => $brand->id,
                    'category_id' => $category->id,
                    'name' => ['en' => $productName, 'ar' => $this->arabicProductName($productName)],
                    'slug' => Str::slug($brandName.' '.$productName.' '.$sku),
                    'gender' => $gender,
                    'type' => $type,
                    'short_description' => [
                        'en' => 'A curated luxury fragrance selected for rarity, presence, and personal expression.',
                        'ar' => 'عطر فاخر مختار بعناية لمن يبحث عن الندرة والحضور والتعبير الشخصي.',
                    ],
                    'description' => [
                        'en' => "Maison De Mystere presents {$productName}, a refined choice for UAE fragrance lovers seeking craftsmanship, elegance, and memorable character.",
                        'ar' => $this->productDescriptionArabic($productName),
                    ],
                    'top_notes' => ['en' => $this->notes($type, 'top'), 'ar' => $this->arabicNotes($type, 'top')],
                    'middle_notes' => ['en' => $this->notes($type, 'middle'), 'ar' => $this->arabicNotes($type, 'middle')],
                    'base_notes' => ['en' => $this->notes($type, 'base'), 'ar' => $this->arabicNotes($type, 'base')],
                    'price' => $price,
                    'sale_price' => $index % 7 === 0 ? round($price * 0.9, 2) : null,
                    'stock_quantity' => 12 + $index,
                    'featured_image_path' => 'images/products/perfume-'.(($index % 4) + 1).'.jpeg',
                    'is_featured' => $index < 8,
                    'is_best_seller' => in_array($sku, ['AR1001', 'LV1006', 'AMG01', 'KN02'], true),
                    'is_new_arrival' => $index >= 10,
                    'is_uae_exclusive' => str_starts_with($sku, 'MDM') || $index % 5 === 0,
                    'is_active' => true,
                    'vat_taxable' => true,
                    'weight_grams' => $type === 'gift_set' ? 1200 : 550,
                    'average_rating' => 4.5,
                    'reviews_count' => 2,
                    'published_at' => now()->subDays($index),
                ],
            );

            foreach ([50, 75, 100] as $size) {
                $product->variants()->updateOrCreate(
                    ['size_label' => "{$size}ml"],
                    [
                        'size_ml' => $size,
                        'sku' => "{$sku}-{$size}",
                        'price' => round($price * ($size / 100), 2),
                        'stock_quantity' => 8,
                        'weight_grams' => 300 + $size * 3,
                        'is_active' => true,
                    ],
                );
            }

            $product->images()->updateOrCreate(
                ['path' => $product->featured_image_path],
                [
                    'alt_text' => ['en' => "{$productName} perfume bottle", 'ar' => "زجاجة عطر {$productName}"],
                    'is_featured' => true,
                ],
            );
        }

        Product::all()->each(function (Product $product): void {
            $related = Product::query()->where('id', '!=', $product->id)->inRandomOrder()->take(4)->pluck('id');
            $product->relatedProducts()->sync($related);
        });

        Coupon::updateOrCreate(
            ['code' => 'MDM10'],
            [
                'name' => ['en' => 'Maison De Mystere Welcome', 'ar' => 'ترحيب ميزون دي ميستير'],
                'type' => 'percentage',
                'value' => 10,
                'min_order_amount' => 300,
                'usage_limit' => 250,
                'is_active' => true,
                'expires_at' => now()->addMonths(6),
            ],
        );

        Banner::updateOrCreate(
            ['location' => 'home', 'sort_order' => 1],
            [
                'title' => ['en' => 'Maison De Mystere UAE Exclusives', 'ar' => 'حصريات ميزون دي ميستير في الإمارات'],
                'subtitle' => [
                    'en' => 'Boutique-exclusive launches and controlled quantities for fragrance collectors.',
                    'ar' => 'إطلاقات حصرية للمتجر وكميات محدودة لهواة اقتناء العطور.',
                ],
                'image_path' => 'images/products/perfume-4.jpeg',
                'link_url' => '/products',
                'is_active' => true,
            ],
        );

        $customer = User::where('email', 'customer@example.com')->first();
        Product::query()->take(4)->get()->each(function (Product $product) use ($customer): void {
            Review::updateOrCreate(
                ['product_id' => $product->id, 'user_id' => $customer?->id],
                [
                    'rating' => 5,
                    'title' => ['en' => 'Beautifully curated', 'ar' => 'تنسيق راق جدا'],
                    'body' => [
                        'en' => 'Elegant presentation, lasting fragrance, and delivery felt premium from start to finish.',
                        'ar' => 'التقديم أنيق جدا، والثبات ممتاز، وتجربة التوصيل بدت فاخرة من البداية حتى النهاية.',
                    ],
                    'is_verified_purchase' => true,
                    'status' => 'approved',
                    'approved_at' => now(),
                ],
            );
        });
    }

    private function notes(string $type, string $level): array
    {
        return match ($type) {
            'oud' => match ($level) {
                'top' => ['Saffron', 'Pink pepper'],
                'middle' => ['Rose', 'Smoked woods'],
                default => ['Oud', 'Amber', 'Leather'],
            },
            'attar' => match ($level) {
                'top' => ['Bergamot', 'Neroli'],
                'middle' => ['Taif rose', 'Jasmine'],
                default => ['Musk', 'Sandalwood'],
            },
            'gift_set' => match ($level) {
                'top' => ['Citrus', 'Cardamom'],
                'middle' => ['Rose', 'Incense'],
                default => ['Amber', 'Oud', 'Vanilla'],
            },
            default => match ($level) {
                'top' => ['Bergamot', 'Saffron'],
                'middle' => ['Rose', 'Jasmine', 'Iris'],
                default => ['Amber', 'Musk', 'Patchouli'],
            },
        };
    }

    private function arabicNotes(string $type, string $level): array
    {
        return match ($type) {
            'oud' => match ($level) {
                'top' => ['زعفران', 'فلفل وردي'],
                'middle' => ['ورد', 'أخشاب مدخنة'],
                default => ['عود', 'عنبر', 'جلد'],
            },
            'attar' => match ($level) {
                'top' => ['برغموت', 'نيرولي'],
                'middle' => ['ورد طائفي', 'ياسمين'],
                default => ['مسك', 'خشب الصندل'],
            },
            'gift_set' => match ($level) {
                'top' => ['حمضيات', 'هيل'],
                'middle' => ['ورد', 'لبان'],
                default => ['عنبر', 'عود', 'فانيلا'],
            },
            default => match ($level) {
                'top' => ['برغموت', 'زعفران'],
                'middle' => ['ورد', 'ياسمين', 'سوسن'],
                default => ['عنبر', 'مسك', 'باتشولي'],
            },
        };
    }

    private function categoryDescriptionArabic(string $categoryName): string
    {
        return "تشكيلة مختارة من {$categoryName} لسوق العطور في الإمارات.";
    }

    private function brandDescriptionArabic(string $brandName): string
    {
        return "عطور مختارة من {$brandName} بعناية لعملاء ميزون دي ميستير.";
    }

    private function productDescriptionArabic(string $productName): string
    {
        return "تقدم ميزون دي ميستير {$productName} كخيار راق لعشاق العطور في الإمارات الباحثين عن الحرفية والأناقة والطابع الذي لا ينسى.";
    }

    private function arabicProductName(string $name): string
    {
        return 'عطر '.$name;
    }
}
