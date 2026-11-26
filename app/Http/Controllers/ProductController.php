<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Services\ProductService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends Controller
{

    public function __construct(
        private readonly ProductService $productService,
    ) {}


    public function home(): View
    {
        $data = $this->productService->getHomeData();

        return view('home.index', $data);
    }


    public function list(Request $request): View|JsonResponse
    {
        $filters = [
            'group'       => $request->route('group'),
            'scope'       => $request->route('scope'),
            'q'           => $request->query('q'),
            'category_id' => $request->query('category_id'),
            'status'      => $request->query('status'),
            'price'       => $request->query('price'),
            'discount'    => $request->query('discount'),
            'gender'      => $request->query('gender'),
            'size'        => $request->query('size'),
            'type'        => $request->query('type'),
            'page'        => (int) $request->query('page', 1),
        ];

        $result = $this->productService->list($filters);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['data' => $result]);
        }

        if ($filters['scope'] === 'admin') {
            $products = $result['products'] ?? $result;
            $categories = $result['categories'] ?? [];

            return view('admin.products.index', compact('products', 'categories', 'filters'));
        }

        $products = $result['products'] ?? $result;
        $categories = $result['categories'] ?? [];
        $sizeOptions = $result['sizeOptions'] ?? [];

        if ($filters['group'] === 'accessory') {
            return view('accessories.index', compact('products', 'categories', 'filters'));
        }

        return view('products.index', compact('products', 'categories', 'sizeOptions', 'filters'));
    }

    public function getById(int $id): View
    {
        $detail = $this->productService->getById($id);

        if (!$detail) {
            throw new NotFoundHttpException('Không tìm thấy sản phẩm.');
        }

        $product = $detail['product'] ?? $detail;
        $relatedProducts = $detail['relatedProducts'] ?? collect();
        $totalStock = $detail['totalStock'] ?? 0;

        return view('products.show', compact('product', 'relatedProducts', 'totalStock'));
    }

    public function showCreateForm(): View
    {
        $categories = $this->productService->getFormCategories();

        return view('admin.products.create', compact('categories'));
    }

    public function create(ProductRequest $request): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }

        $product = $this->productService->create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Thêm sản phẩm thành công.',
                'data'    => $product,
            ], 201);
        }

        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công.');
    }

    public function showEditForm(int $id): View
    {
        $detail = $this->productService->getById($id);

        if (!$detail) {
            throw new NotFoundHttpException('Không tìm thấy sản phẩm.');
        }

        $product = $detail['product'] ?? $detail;
        $categories = $this->productService->getFormCategories();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, int $id): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }

        $product = $this->productService->update($id, $data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Cập nhật sản phẩm thành công.',
                'data'    => $product,
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công.');
    }

    public function showStock(int $id): View
    {
        $detail = $this->productService->getById($id);

        if (!$detail) {
            throw new NotFoundHttpException('Không tìm thấy sản phẩm.');
        }

        $product = $detail['product'] ?? $detail;

        return view('admin.products.stock', compact('product'));
    }

    public function updateStock(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'stocks'   => 'required|array',
            'stocks.*' => 'required|integer|min:0',
        ]);

        $product = $this->productService->updateStock($id, $validated['stocks']);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Cập nhật tồn kho thành công.',
                'data'    => $product,
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật tồn kho thành công.');
    }

    public function close(int $id): RedirectResponse|JsonResponse
    {
        $this->productService->close($id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['message' => 'Đã xóa sản phẩm.']);
        }

        return redirect()->route('admin.products.index')->with('success', 'Đã xóa sản phẩm.');
    }
}
