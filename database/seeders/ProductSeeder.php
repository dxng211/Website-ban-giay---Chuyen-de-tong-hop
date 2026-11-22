<?php

namespace Database\Seeders;

use App\Enums\Gender;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Dữ liệu mẫu có chủ đích để thử các tình huống:
     * - Có sản phẩm giảm giá (sale_price) và không giảm giá.
     * - Có size hết hàng (Samba size 41, tất cổ thấp size L) để thử ô size bị mờ.
     * - Phụ kiện không có size dùng size 'Mặc định'; dây giày có nhiều độ dài.
     * - Mô tả có từ khóa mục đích (chạy bộ, đi học, thời trang...) để Gemini dựa vào khi chọn sản phẩm.
     * - Chatbot: chân 24.8 cm (size 40), giá 1.000.000 - 2.000.000 sẽ ra Converse, Vans, Puma.
     *
     * Ảnh dùng placehold.co (cần Internet). Thay bằng ảnh thật bằng cách đặt vào storage/app/public/products
     * rồi lưu image_url dạng 'products/ten-anh.jpg'.
     */
    public function run(): void
    {
        $cats = Category::pluck('id', 'slug');

        $products = [
            // ------------------------------ GIÀY ------------------------------
            [
                'category' => 'giay', 'sku' => 'ADS-SAMBA-OG', 'name' => 'Adidas Samba OG', 'gender' => Gender::Unisex,
                'base_price' => 2500000, 'sale_price' => 2100000, 'sold_count' => 120,
                'description' => 'Giày sneaker phong cách retro, thiết kế cổ điển. Phù hợp đi học, đi chơi, mang hằng ngày.',
                'stock' => ['38' => 8, '39' => 10, '40' => 12, '41' => 0, '42' => 6, '43' => 5, '44' => 3],
            ],
            [
                'category' => 'giay', 'sku' => 'NIKE-PEG-41', 'name' => 'Nike Pegasus 41', 'gender' => Gender::Male,
                'base_price' => 3200000, 'sale_price' => null, 'sold_count' => 85,
                'description' => 'Giày chạy bộ nhẹ, đệm êm, thoáng khí. Phù hợp chạy bộ, tập luyện thể thao hằng ngày.',
                'stock' => ['40' => 7, '41' => 9, '42' => 11, '43' => 8, '44' => 4],
            ],
            [
                'category' => 'giay', 'sku' => 'NIKE-AF1-07', 'name' => "Nike Air Force 1 '07", 'gender' => Gender::Unisex,
                'base_price' => 2900000, 'sale_price' => null, 'sold_count' => 95,
                'description' => 'Giày sneaker thời trang đế cao, dễ phối đồ. Phù hợp đi chơi, đi làm, phong cách thời trang đường phố.',
                'stock' => ['38' => 5, '39' => 6, '40' => 8, '41' => 7, '42' => 9, '43' => 4],
            ],
            [
                'category' => 'giay', 'sku' => 'PUMA-SPEEDCAT', 'name' => 'Puma Speedcat', 'gender' => Gender::Female,
                'base_price' => 2300000, 'sale_price' => 1950000, 'sold_count' => 40,
                'description' => 'Giày thời trang nữ dáng thon, phong cách thể thao nhẹ nhàng. Phù hợp đi chơi, đi học.',
                'stock' => ['36' => 6, '37' => 8, '38' => 9, '39' => 7, '40' => 5],
            ],
            [
                'category' => 'giay', 'sku' => 'ASICS-GEL-KAYANO', 'name' => 'Asics Gel-Kayano', 'gender' => Gender::Male,
                'base_price' => 3800000, 'sale_price' => null, 'sold_count' => 30,
                'description' => 'Giày chạy bộ ổn định, hỗ trợ bàn chân. Phù hợp chạy đường dài, tập gym.',
                'stock' => ['40' => 4, '41' => 5, '42' => 6, '43' => 5, '44' => 3],
            ],
            [
                'category' => 'giay', 'sku' => 'CVS-CHUCK70', 'name' => 'Converse Chuck 70', 'gender' => Gender::Unisex,
                'base_price' => 1800000, 'sale_price' => 1500000, 'sold_count' => 150,
                'description' => 'Giày vải cổ điển, dễ phối đồ. Phù hợp đi học, đi chơi, phong cách thời trang.',
                'stock' => ['36' => 5, '37' => 7, '38' => 8, '39' => 9, '40' => 10, '41' => 8, '42' => 6, '43' => 4],
            ],
            [
                'category' => 'giay', 'sku' => 'VANS-OLDSKOOL', 'name' => 'Vans Old Skool', 'gender' => Gender::Unisex,
                'base_price' => 1700000, 'sale_price' => null, 'sold_count' => 110,
                'description' => 'Giày skate bền, đế bám tốt. Phù hợp đi học, trượt ván, mang hằng ngày.',
                'stock' => ['36' => 4, '37' => 6, '38' => 8, '39' => 8, '40' => 9, '41' => 7, '42' => 5, '43' => 3, '44' => 2],
            ],

            // ---------------------------- PHỤ KIỆN ----------------------------
            [
                'category' => 'tat', 'sku' => 'SOCK-CREW', 'name' => 'Tất cổ cao basic',
                'base_price' => 60000, 'sale_price' => null, 'sold_count' => 200,
                'description' => 'Tất cotton cổ cao, thấm hút mồ hôi, phù hợp chơi thể thao và mang hằng ngày.',
                'stock' => ['S' => 30, 'M' => 40, 'L' => 25],
            ],
            [
                'category' => 'tat', 'sku' => 'SOCK-ANKLE', 'name' => 'Tất cổ thấp',
                'base_price' => 45000, 'sale_price' => 39000, 'sold_count' => 150,
                'description' => 'Tất cổ thấp mỏng nhẹ, phù hợp mang với giày sneaker.',
                'stock' => ['S' => 20, 'M' => 30, 'L' => 0],
            ],
            [
                'category' => 'lot-giay', 'sku' => 'INSOLE-COMFORT', 'name' => 'Lót giày êm chân',
                'base_price' => 120000, 'sale_price' => null, 'sold_count' => 70,
                'description' => 'Lót giày đệm mềm, giảm đau chân khi đi bộ hoặc đứng lâu.',
                'stock' => ['38' => 15, '39' => 15, '40' => 20, '41' => 20, '42' => 15, '43' => 10],
            ],
            [
                'category' => 've-sinh-giay', 'sku' => 'CLEAN-SPRAY', 'name' => 'Chai vệ sinh giày khô',
                'base_price' => 150000, 'sale_price' => 129000, 'sold_count' => 90,
                'description' => 'Dung dịch vệ sinh giày dạng bọt khô, làm sạch nhanh, không cần xả nước.',
                'stock' => ['Mặc định' => 50],
            ],
            [
                'category' => 'day-giay', 'sku' => 'LACE-FLAT', 'name' => 'Dây giày dẹt',
                'base_price' => 50000, 'sale_price' => null, 'sold_count' => 60,
                'description' => 'Dây giày dẹt bền, nhiều độ dài phù hợp từng loại giày.',
                'stock' => ['90cm' => 40, '120cm' => 40, '150cm' => 25],
            ],
        ];

        foreach ($products as $p) {
            $product = Product::updateOrCreate(['sku' => $p['sku']], [
                'category_id' => $cats[$p['category']],
                'name' => $p['name'],
                'description' => $p['description'],
                'gender' => $p['gender'] ?? null, // phụ kiện không có giới tính
                'base_price' => $p['base_price'],
                'sale_price' => $p['sale_price'],
                'is_active' => true,
            ]);

            // sold_count không nằm trong $fillable (Observer cập nhật) nên dùng forceFill
            $product->forceFill(['sold_count' => $p['sold_count']])->save();

            foreach ($p['stock'] as $size => $qty) {
                $size = (string) $size;
                ProductVariant::updateOrCreate(
                    ['product_id' => $product->id, 'size' => $size],
                    [
                        'variant_sku' => $p['sku'] . '-' . Str::upper(Str::slug($size)),
                        'stock_quantity' => $qty,
                    ]
                );
            }

            if ($product->images()->count() === 0) {
                foreach (range(1, 3) as $i) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => 'https://placehold.co/600x600/png?text=' . urlencode($p['name'] . ' ' . $i),
                        'sort_order' => $i - 1, // 0 = ảnh khung lớn mặc định
                    ]);
                }
            }
        }
    }
}
