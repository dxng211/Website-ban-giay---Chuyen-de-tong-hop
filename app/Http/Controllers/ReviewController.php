<?php

namespace App\Http\Controllers;

use App\Enums\ReviewStatus;
use App\Http\Requests\ReviewRequest;
use App\Services\ReviewService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReviewController extends Controller
{

    public function __construct(
        private readonly ReviewService $reviewService,
    ) {}

    
    public function create(ReviewRequest $request): JsonResponse
    {
        try {
            $review = $this->reviewService->create(Auth::user(), $request->validated());

            return response()->json([
                'message' => 'Đánh giá đã gửi và đang chờ duyệt.',
                'data'    => $review,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage() ?: 'Cần mua sản phẩm trước khi đánh giá.',
            ], 422);
        }
    }

    public function listByProduct(Request $request, int $id): JsonResponse
    {
        $rating = $request->query('rating');
        $reviews = $this->reviewService->listByProduct(
            $id,
            is_numeric($rating) ? (int) $rating : null,
        );

        return response()->json(['data' => $reviews]);
    }

    public function list(Request $request): View
    {
        $filters = [
            'status' => $request->query('status'),
            'rating' => $request->query('rating'),
        ];

        $status = $filters['status'];
        $reviews = $this->reviewService->list(
            is_string($status) ? ReviewStatus::tryFrom($status) : null,
        );

        return view('admin.reviews.index', compact('reviews', 'filters'));
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(ReviewStatus::class)],
        ]);

        $review = $this->reviewService->updateStatus($id, $validated['status']);

        return response()->json([
            'message' => 'Cập nhật trạng thái đánh giá thành công.',
            'data'    => $review,
        ]);
    }
}
