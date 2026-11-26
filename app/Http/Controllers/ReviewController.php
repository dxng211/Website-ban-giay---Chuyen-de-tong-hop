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
        try {
            $this->reviewService->create($request->user(), $request->validated());

            return response()->json([
                'message' => 'Đã gửi đánh giá thành công, đang chờ quản trị viên kiểm duyệt.',
            ], 201);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * GET /products/{id}/reviews (AJAX): lọc theo số sao, tải thêm
     */
    public function listByProduct(Request $request, int $id): JsonResponse
    {
        // TODO: đọc ?rating= và ?page=; chỉ trả đánh giá đã duyệt, mới nhất trước
        $rating = (int) $request->query('rating', 0);
        $reviews = $this->reviewService->listByProduct($id, $rating > 0 ? $rating : null, 5);

        $html = view('products.partials._review-list', [
            'reviews' => $reviews,
            'isAppend' => $reviews->currentPage() > 1,
        ])->render();

        return response()->json([
            'html' => $html,
            'has_more' => $reviews->hasMorePages(),
            'next_page' => $reviews->currentPage() + 1,
            'total' => $reviews->total(),
        ]);
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
