<?php

namespace App\Repositories;

use App\Enums\ReviewStatus;
use App\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Truy vấn bảng reviews.
 */
class ReviewRepository
{
    /**
     * Lưu đánh giá, status = pending (Seq07).
     */
    public function save(array $data): Review
    {
        // TODO: Review::create($data)
        return Review::create($data);
    }

    /**
     * Đánh giá đã duyệt của sản phẩm, lọc theo số sao, mới nhất trước, phân trang.
     */
    public function listByProduct(int $productId, ?int $rating = null, int $perPage = 5): LengthAwarePaginator
    {
        // TODO: Review::approved()->...
        return Review::query()
            ->with('user')
            ->where('product_id', $productId)
            ->where('status', ReviewStatus::Approved)
            ->when($rating && $rating >= 1 && $rating <= 5, fn ($q) => $q->where('rating', $rating))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * Danh sách cho Admin, lọc theo trạng thái - thêm mới: hàm chưa có trong Class Diagram.
     */
    public function list(?ReviewStatus $status = null, int $perPage = 15): LengthAwarePaginator
    {
        // TODO: viết truy vấn
    }

    /**
     * Đổi trạng thái đánh giá.
     */
    public function updateStatus(int $id, ReviewStatus $status): Review
    {
        // TODO: update qua Model để ReviewObserver chạy
    }
}
