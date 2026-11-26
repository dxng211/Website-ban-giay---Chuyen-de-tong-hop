<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Services\ProductService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //
    public function __construct(
        private readonly ProductService $productService,
    ) {}

    /**
     * GET / (thêm mới: hàm chưa có trong Class Diagram)
     */
    public function home(): View
    {
        // TODO: lấy sản phẩm bán chạy và mới nhất từ $this->productService->getHomeData()
        $data = $this->productService->getHomeData();

        return view('home.index', $data);
    }

    /**
     * GET /products, /accessories, /admin/products
     * tham số route: $request->route('group') = 'shoe' | 'accessory'; $request->route('scope') = 'admin'
     */
    public function list(Request $request): View|JsonResponse
    {
        // TODO: đọc bộ lọc từ query string: giá (asc/desc), giảm giá, giới tính, size, loại phụ kiện, trang
        // TODO: gọi $this->productService->list($filters); AJAX -> JSON, thường -> view
        $scope = $request->route('scope');
        $group = $request->route('group')
            ?? ($scope === 'admin' ? null : ($request->routeIs('accessories.*') ? 'accessory' : 'shoe'));

        $filters = array_merge(
            $request->only(['sort', 'gender', 'size', 'category', 'keyword', 'is_active']),
            [
                'group' => $group,
                'scope' => $scope,
            ]
        );

        $products = $this->productService->list($filters);

        if ($request->expectsJson()) {
            return response()->json($products);
        }

        if ($scope === 'admin') {
            return view('admin.products.index', compact('products', 'filters'));
        }

        if ($group === 'accessory') {
            $accessoryCategories = $this->productService->getAccessoryCategories();

            return view('accessories.index', compact('products', 'filters', 'accessoryCategories'));
        }

        $sizes = $this->productService->getInStockShoeSizes();

        return view('products.index', compact('products', 'filters', 'sizes'));
    }

    /**
     * GET /products/{id} (Seq01)
     */
    public function getById(int $id): View
    {
        // TODO: gọi $this->productService->getById($id); không thấy -> NotFoundException (404)
        $product = $this->productService->getById($id);

        $approvedReviewsQuery = $product->reviews()->where('status', \App\Enums\ReviewStatus::Approved);

        $ratingCounts = [
            0 => (clone $approvedReviewsQuery)->count(),
            5 => (clone $approvedReviewsQuery)->where('rating', 5)->count(),
            4 => (clone $approvedReviewsQuery)->where('rating', 4)->count(),
            3 => (clone $approvedReviewsQuery)->where('rating', 3)->count(),
            2 => (clone $approvedReviewsQuery)->where('rating', 2)->count(),
            1 => (clone $approvedReviewsQuery)->where('rating', 1)->count(),
        ];

        $initialReviews = $product->reviews()
            ->with('user')
            ->where('status', \App\Enums\ReviewStatus::Approved)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(5);

        return view('products.show', compact('product', 'ratingCounts', 'initialReviews'));
    }

    /**
     * POST /admin/products (Seq06)
     */
    public function create(ProductRequest $request): RedirectResponse|JsonResponse
    {
        // TODO: gọi $this->productService->create($request->validated()) kèm file ảnh
    }

    /**
     * PUT /admin/products/{id} (Seq06)
     */
    public function update(ProductRequest $request, int $id): RedirectResponse|JsonResponse
    {
        // TODO: gọi $this->productService->update($id, $request->validated())
    }

    /**
     * PATCH /admin/products/{id}/stock
     */
    public function updateStock(Request $request, int $id): RedirectResponse|JsonResponse
    {
        // TODO: validate mảng tồn kho theo size rồi gọi $this->productService->updateStock($id, $stocks)
    }

    /**
     * DELETE /admin/products/{id} (xóa mềm)
     */
    public function close(int $id): RedirectResponse|JsonResponse
    {
        // TODO: gọi $this->productService->close($id)
    }
}
