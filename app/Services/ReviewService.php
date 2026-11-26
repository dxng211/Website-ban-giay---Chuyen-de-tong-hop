<?php

namespace App\Services;

use App\Enums\ReviewStatus;
use App\Models\Review;
use App\Models\User;
use App\Repositories\OrderRepository;
use App\Repositories\ReviewRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
        // TODO: $this->orders->hasPurchased($user->id, $data['product_id']); chưa mua -> lỗi nghiệp vụ
        // TODO: $this->reviews->save(...)
        $productId = (int) $data['product_id'];

        if (! $this->orders->hasPurchased($user->id, $productId)) {
            throw new \DomainException('Cần mua sản phẩm trước khi đánh giá.');
        }

        return $this->reviews->save([
            'product_id' => $productId,
            'user_id' => $user->id,
            'order_id' => $data['order_id'] ?? null,
            'rating' => (int) $data['rating'],
            'comment' => trim((string) $data['comment']),
            'status' => ReviewStatus::Pending,
        ]);
    }

    /**
     * Đánh giá đã duyệt của một sản phẩm, lọc theo số sao, mới nhất trước, có phân trang.
     */
    public function listByProduct(int $productId, ?int $rating = null, int $perPage = 5): LengthAwarePaginator
    {
        // TODO: $this->reviews->listByProduct(...)
        return $this->reviews->listByProduct($productId, $rating, $perPage);
    }

    /**
     * Admin xem tất cả đánh giá (thêm mới: hàm chưa có trong Class Diagram).
     */
    public function list(?ReviewStatus $status = null, int $perPage = 15): LengthAwarePaginator
    {
        // TODO: $this->reviews->list(...)
    }

    /**
     * Admin duyệt hoặc từ chối. Observer tự tính lại rating_avg và review_count.
     */
    public function updateStatus(int $id, ReviewStatus $status): Review
    {
        // TODO: $this->reviews->updateStatus($id, $status)
    }
}
