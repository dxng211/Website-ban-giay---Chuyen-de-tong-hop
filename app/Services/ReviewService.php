<?php

namespace App\Services;

use App\Enums\ReviewStatus;
use App\Models\Review;
use App\Models\User;
use App\Repositories\OrderRepository;
use App\Repositories\ReviewRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

/**
 * Phụ thuộc: ReviewRepository, OrderRepository.
 */
class ReviewService
{
    public function __construct(
        private readonly ReviewRepository $reviews,
        private readonly OrderRepository $orders,
    ) {}

    /**
     * Gửi đánh giá (UC07, Seq07): chỉ khi đã mua sản phẩm (đơn hoàn thành). Lưu với status = pending.
     */
    public function create(User $user, array $data): Review
    {
        $productId = (int) $data['product_id'];

        if (!$this->orders->hasPurchased($user->id, $productId)) {
            throw ValidationException::withMessages([
                'product_id' => 'Bạn chỉ có thể đánh giá sản phẩm sau khi đơn hàng đã hoàn thành.',
            ]);
        }

        return $this->reviews->save([
            'user_id' => $user->id,
            'product_id' => $productId,
            'rating' => (int) $data['rating'],
            'comment' => $data['comment'] ?? null,
            'status' => ReviewStatus::Pending,
        ]);
    }

    /**
     * Đánh giá đã duyệt của một sản phẩm, lọc theo số sao, mới nhất trước, có phân trang.
     */
    public function listByProduct(int $productId, ?int $rating = null, int $perPage = 5): LengthAwarePaginator
    {
        return $this->reviews->listByProduct($productId, $rating, $perPage);
    }

    /**
     * Admin xem tất cả đánh giá (thêm mới: hàm chưa có trong Class Diagram).
     */
    public function list(?ReviewStatus $status = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->reviews->list($status, $perPage);
    }

    /**
     * Admin duyệt hoặc từ chối. Observer tự tính lại rating_avg và review_count.
     */
    public function updateStatus(int $id, ReviewStatus $status): Review
    {
        return $this->reviews->updateStatus($id, $status);
    }
}
