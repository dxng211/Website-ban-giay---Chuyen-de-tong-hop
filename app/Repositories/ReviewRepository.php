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
    }

    /**
     * Đánh giá đã duyệt của sản phẩm, lọc theo số sao, mới nhất trước, phân trang.
     */
    public function listByProduct(int $productId, ?int $rating = null, int $perPage = 5): LengthAwarePaginator
    {
        // TODO: Review::approved()->...
    }

    /**
     * Danh sách cho Admin, lọc theo trạng thái - thêm mới: hàm chưa có trong Class Diagram.
     */
    /**
     * Danh sách cho Admin, lọc theo trạng thái - thêm mới: hàm chưa có trong Class Diagram.
     */
    public function list(?ReviewStatus $status = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = \App\Models\Review::query();

        $relations = [];
        if (method_exists(\App\Models\Review::class, 'user')) {
            $relations[] = 'user';
        }
        if (method_exists(\App\Models\Review::class, 'product')) {
            $relations[] = 'product';
        }
        if (!empty($relations)) {
            $query->with($relations);
        }

        if ($status !== null) {
            $column = \Illuminate\Support\Facades\Schema::hasColumn('reviews', 'status')
                ? 'status'
                : (\Illuminate\Support\Facades\Schema::hasColumn('reviews', 'review_status') ? 'review_status' : 'status');

            $query->where($column, $status->value ?? $status);
        }

        $perPage = $perPage > 0 ? min($perPage, 100) : 15;

        return $query->latest('id')->paginate($perPage)->withQueryString();
    }

    /**
     * Đổi trạng thái đánh giá.
     */
    public function updateStatus(int $id, ReviewStatus $status): Review
    {
        // TODO: update qua Model để ReviewObserver chạy
    }
}
