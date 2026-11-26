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
     * - Số lượng đủ lớn (15 giày, 14 phụ kiện) để test phân trang 12 sản phẩm/trang ở cả 2 trang.
     */
    public function run(): void
    {
        $cats = Category::pluck('id', 'slug');

        $products = [
            // ------------------------------ GIÀY (15 mẫu -> 2 trang) ------------------------------
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
            // --- Giày bổ sung để test phân trang ---
            [
                'category' => 'giay', 'sku' => 'NB-530-WHITE', 'name' => 'New Balance 530 White Silver', 'gender' => Gender::Unisex,
                'base_price' => 2650000, 'sale_price' => 2290000, 'sold_count' => 135,
                'description' => 'Giày chạy bộ phong cách dad-shoe thoáng khí, đế ABZORB êm ái. Phù hợp đi chơi, đi làm.',
                'stock' => ['37' => 5, '38' => 7, '39' => 9, '40' => 11, '41' => 8, '42' => 6],
            ],
            [
                'category' => 'giay', 'sku' => 'ADS-ULTRABOOST', 'name' => 'Adidas Ultraboost Light', 'gender' => Gender::Male,
                'base_price' => 4200000, 'sale_price' => 3590000, 'sold_count' => 72,
                'description' => 'Giày chạy bộ hiệu năng cao, hoàn trả năng lượng tốt. Phù hợp chạy marathon và tập gym.',
                'stock' => ['40' => 6, '41' => 8, '42' => 7, '43' => 5, '44' => 2],
            ],
            [
                'category' => 'giay', 'sku' => 'NIKE-DUNK-LOW', 'name' => 'Nike Dunk Low Panda', 'gender' => Gender::Unisex,
                'base_price' => 2800000, 'sale_price' => null, 'sold_count' => 140,
                'description' => 'Giày sneaker phối màu đen trắng kinh điển, dễ phối mọi trang phục.',
                'stock' => ['36' => 4, '37' => 6, '38' => 8, '39' => 10, '40' => 9, '41' => 6, '42' => 4],
            ],
            [
                'category' => 'giay', 'sku' => 'ADS-GAZELLE-W', 'name' => 'Adidas Gazelle Indoor', 'gender' => Gender::Female,
                'base_price' => 2600000, 'sale_price' => 2190000, 'sold_count' => 64,
                'description' => 'Giày da lộn cổ điển với đế gum nổi bật, tôn dáng bàn chân nữ.',
                'stock' => ['36' => 7, '37' => 9, '38' => 8, '39' => 5],
            ],
            [
                'category' => 'giay', 'sku' => 'MLB-CHUNKY-LINER', 'name' => 'MLB Chunky Liner NY', 'gender' => Gender::Unisex,
                'base_price' => 3100000, 'sale_price' => 2690000, 'sold_count' => 88,
                'description' => 'Giày sneaker đế độn nhẹ tăng chiều cao, phong cách trẻ trung năng động.',
                'stock' => ['36' => 5, '37' => 6, '38' => 7, '39' => 8, '40' => 6, '41' => 5],
            ],
            [
                'category' => 'giay', 'sku' => 'ASICS-COURT-MZ', 'name' => 'Asics Court MZ Cream', 'gender' => Gender::Female,
                'base_price' => 2150000, 'sale_price' => null, 'sold_count' => 53,
                'description' => 'Giày sneaker màu kem nhẹ nhàng, đế êm, phù hợp đi học và dạo phố.',
                'stock' => ['36' => 6, '37' => 8, '38' => 7, '39' => 6, '40' => 4],
            ],
            [
                'category' => 'giay', 'sku' => 'PUMA-PALERMO', 'name' => 'Puma Palermo Leather', 'gender' => Gender::Unisex,
                'base_price' => 2250000, 'sale_price' => 1890000, 'sold_count' => 47,
                'description' => 'Giày da trơn mũi chữ T cổ điển, bền bỉ và dễ vệ sinh.',
                'stock' => ['38' => 5, '39' => 7, '40' => 8, '41' => 6, '42' => 5, '43' => 3],
            ],
            [
                'category' => 'giay', 'sku' => 'NB-327-GREY', 'name' => 'New Balance 327 Sea Salt', 'gender' => Gender::Female,
                'base_price' => 2450000, 'sale_price' => null, 'sold_count' => 61,
                'description' => 'Giày sneaker thiết kế đế gai độc đáo, trọng lượng siêu nhẹ cho nữ.',
                'stock' => ['36' => 5, '37' => 7, '38' => 6, '39' => 5],
            ],

            // ---------------------------- PHỤ KIỆN (14 mẫu -> 2 trang) ----------------------------
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
            // --- Phụ kiện bổ sung để test phân trang ---
            [
                'category' => 'tat', 'sku' => 'SOCK-NO-SHOW', 'name' => 'Tất tàng hình chống tuột gót',
                'base_price' => 55000, 'sale_price' => 45000, 'sold_count' => 175,
                'description' => 'Tất lười có đệm silicon sau gót chống tuột, phù hợp đi giày lười và sneaker cổ thấp.',
                'stock' => ['S' => 25, 'M' => 35, 'L' => 20],
            ],
            [
                'category' => 'tat', 'sku' => 'SOCK-SPORT-PRO', 'name' => 'Tất thể thao nén bắp chân',
                'base_price' => 95000, 'sale_price' => null, 'sold_count' => 82,
                'description' => 'Tất thể thao dày dặn, hỗ trợ vòm bàn chân khi chạy bộ và tập gym.',
                'stock' => ['M' => 30, 'L' => 25],
            ],
            [
                'category' => 'lot-giay', 'sku' => 'INSOLE-ORTHO', 'name' => 'Lót giày thể thao Ortholite',
                'base_price' => 160000, 'sale_price' => 135000, 'sold_count' => 94,
                'description' => 'Lót giày thoáng khí khử mùi, đàn hồi cao dành cho giày chạy bộ.',
                'stock' => ['38' => 10, '39' => 12, '40' => 18, '41' => 18, '42' => 14, '43' => 8],
            ],
            [
                'category' => 'lot-giay', 'sku' => 'INSOLE-HEIGHT-2CM', 'name' => 'Lót giày tăng chiều cao 2cm',
                'base_price' => 140000, 'sale_price' => null, 'sold_count' => 65,
                'description' => 'Lót đệm gót nâng chiều cao tự nhiên, không gây dốc chân.',
                'stock' => ['38' => 12, '39' => 15, '40' => 15, '41' => 12, '42' => 10],
            ],
            [
                'category' => 've-sinh-giay', 'sku' => 'CLEAN-KIT-PRO', 'name' => 'Bộ vệ sinh giày 3 món kèm bàn chải',
                'base_price' => 250000, 'sale_price' => 210000, 'sold_count' => 112,
                'description' => 'Bộ gồm dung dịch tạo bọt, bàn chải lông mềm và khăn lau microfiber.',
                'stock' => ['Mặc định' => 35],
            ],
            [
                'category' => 've-sinh-giay', 'sku' => 'DEODORANT-SPRAY', 'name' => 'Xịt khử mùi và kháng khuẩn giày',
                'base_price' => 130000, 'sale_price' => null, 'sold_count' => 78,
                'description' => 'Xịt khử mùi hương bạc hà mát lạnh, giữ giày luôn khô thoáng.',
                'stock' => ['Mặc định' => 45],
            ],
            [
                'category' => 've-sinh-giay', 'sku' => 'WATERPROOF-SPRAY', 'name' => 'Chai xịt phủ nano chống thấm nước',
                'base_price' => 220000, 'sale_price' => 189000, 'sold_count' => 68,
                'description' => 'Tạo lớp màng bảo vệ giày vải và da lộn khỏi nước mưa và bụi bẩn.',
                'stock' => ['Mặc định' => 30],
            ],
            [
                'category' => 'day-giay', 'sku' => 'LACE-ROUND-REFLECT', 'name' => 'Dây giày tròn phản quang',
                'base_price' => 65000, 'sale_price' => 52000, 'sold_count' => 74,
                'description' => 'Dây giày tròn dệt sợi phản quang nổi bật khi di chuyển buổi tối.',
                'stock' => ['100cm' => 30, '120cm' => 35, '140cm' => 20],
            ],
            [
                'category' => 'day-giay', 'sku' => 'LACE-WAXED', 'name' => 'Dây giày sáp cao cấp chống xù',
                'base_price' => 75000, 'sale_price' => null, 'sold_count' => 48,
                'description' => 'Dây giày phủ sáp bóng nhẹ, giữ form nút thắt chắc chắn.',
                'stock' => ['90cm' => 25, '120cm' => 30],
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