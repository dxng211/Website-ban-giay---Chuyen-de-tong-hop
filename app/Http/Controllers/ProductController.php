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
        return view('home.index', [
        'featuredCategories' => [],
        'bestSellerProducts' => [],
        'newestProducts' => [],
    ]);
    }

    /**
     * GET /products, /accessories, /admin/products
     * tham số route: $request->route('group') = 'shoe' | 'accessory'; $request->route('scope') = 'admin'
     */
    public function list(Request $request): View|JsonResponse
    {
        // TODO: đọc bộ lọc từ query string: giá (asc/desc), giảm giá, giới tính, size, loại phụ kiện, trang
        // TODO: gọi $this->productService->list($filters); AJAX -> JSON, thường -> view
    }

    /**
     * GET /products/{id} (Seq01)
     */
    public function getById(int $id): View
    {
        // TODO: gọi $this->productService->getById($id); không thấy -> NotFoundException (404)
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
