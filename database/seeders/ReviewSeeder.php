<?php

namespace Database\Seeders;

use App\Enums\ReviewStatus;
use App\Enums\Role;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Đánh giá mẫu (order_id để NULL nên bỏ qua quy tắc "phải mua hàng").
     * - Adidas Samba có 7 đánh giá đã duyệt (nhiều hơn 5) để thử nút "hiển thị thêm đánh giá".
     * - Có 1 đánh giá chờ duyệt và 1 bị từ chối để thử màn hình duyệt của Admin.
     */
    public function run(): void
    {
        $customers = User::where('role', Role::Customer->value)->where('is_locked', false)->get();
        if ($customers->isEmpty()) {
            return;
        }

        // [sku, số sao, nội dung, trạng thái, cách đây bao nhiêu ngày]
        $reviews = [
            ['ADS-SAMBA-OG', 5, 'Form đẹp, đi êm chân, rất ưng ý.', ReviewStatus::Approved, 1],
            ['ADS-SAMBA-OG', 4, 'Giày đẹp, giao hàng nhanh.', ReviewStatus::Approved, 3],
            ['ADS-SAMBA-OG', 5, 'Đúng size, đi cả ngày không đau chân.', ReviewStatus::Approved, 5],
            ['ADS-SAMBA-OG', 5, 'Màu sắc giống ảnh, chất lượng tốt.', ReviewStatus::Approved, 8],
            ['ADS-SAMBA-OG', 3, 'Giày ổn nhưng hơi cứng lúc mới mang.', ReviewStatus::Approved, 12],
            ['ADS-SAMBA-OG', 4, 'Phối đồ dễ, đáng tiền.', ReviewStatus::Approved, 15],
            ['ADS-SAMBA-OG', 5, 'Mua lần hai cho bạn, vẫn hài lòng.', ReviewStatus::Approved, 20],
            ['NIKE-PEG-41', 5, 'Chạy bộ rất êm, nhẹ chân.', ReviewStatus::Approved, 2],
            ['NIKE-PEG-41', 4, 'Đệm tốt, thoáng khí.', ReviewStatus::Approved, 6],
            ['CVS-CHUCK70', 4, 'Kiểu dáng cổ điển, dễ phối đồ.', ReviewStatus::Approved, 4],
            ['VANS-OLDSKOOL', 5, 'Đế bám tốt, bền, đi học rất hợp.', ReviewStatus::Approved, 7],
            ['ADS-SAMBA-OG', 4, 'Đánh giá đang chờ Admin duyệt (dữ liệu mẫu).', ReviewStatus::Pending, 0],
            ['CVS-CHUCK70', 1, 'Nội dung không phù hợp (dữ liệu mẫu để thử từ chối).', ReviewStatus::Rejected, 2],
        ];

        $productIds = Product::pluck('id', 'sku');

        foreach ($reviews as $i => [$sku, $rating, $comment, $status, $daysAgo]) {
            if (! isset($productIds[$sku])) {
                continue;
            }

            $review = Review::firstOrNew(['product_id' => $productIds[$sku], 'comment' => $comment]);
            $review->forceFill([
                'user_id' => $customers[$i % $customers->count()]->id,
                'rating' => $rating,
                'status' => $status,
                'created_at' => now()->subDays($daysAgo),
            ])->save();
        }

        // Tính lại rating_avg và review_count từ các đánh giá đã duyệt, không phụ thuộc Observer.
        $stats = Review::approved()
            ->selectRaw('product_id, COUNT(*) as cnt, AVG(rating) as avg_rating')
            ->groupBy('product_id')
            ->get();

        foreach ($stats as $s) {
            Product::whereKey($s->product_id)->update([
                'review_count' => $s->cnt,
                'rating_avg' => round($s->avg_rating, 1),
            ]);
        }
    }
}
