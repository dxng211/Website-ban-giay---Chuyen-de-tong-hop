<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Services\ReviewService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    //
    public function __construct(
        private readonly ReviewService $reviewService,
    ) {}

    /**
     * POST /reviews (Seq07) - route đã có middleware auth
     */
    public function create(ReviewRequest $request): JsonResponse
    {
        // TODO: gọi $this->reviewService->create(Auth::user(), $request->validated()); chưa mua hàng -> lỗi nghiệp vụ
    }

    /**
     * GET /products/{id}/reviews (AJAX): lọc theo số sao, tải thêm
     */
    public function listByProduct(Request $request, int $id): JsonResponse
    {
        // TODO: đọc ?rating= và ?page=; chỉ trả đánh giá đã duyệt, mới nhất trước
    }

    /**
     * GET /admin/reviews (thêm mới: hàm chưa có trong Class Diagram)
     */
    public function list(Request $request): View
    {
        // TODO: Admin xem danh sách đánh giá, lọc theo trạng thái (mặc định chờ duyệt)
    }

    /**
     * PATCH /admin/reviews/{id}/status
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        // TODO: validate status bằng Rule::enum(ReviewStatus::class); gọi $this->reviewService->updateStatus(...)
    }
}
