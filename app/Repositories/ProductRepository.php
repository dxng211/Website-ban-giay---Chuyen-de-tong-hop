<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Truy vấn nhóm bảng products, product_variants, product_images. Dùng các scope trong Model Product.
 */
class ProductRepository
{
    /**
     * Chi tiết sản phẩm kèm ảnh và các size (Seq01).
     */
    public function findWithSizes(int $id): ?Product
    {
        // TODO: Product::with(['images', 'variants'])->find($id)
    }

    /**
     * Danh sách có lọc (giá, giảm giá, giới tính, size, loại) và phân trang. Mặc định bán chạy nhất lên trước.
     */
    public function list(array $filters): LengthAwarePaginator
    {
        // TODO: dùng scope active, inGroup, onSale, hasSizeInStock; orderByDesc('sold_count')
        $query = Product::query()
            ->with([
                'images' => fn ($q) => $q->orderBy('sort_order')->orderBy('id'),
                'category',
            ]);

        // Nếu không phải trang quản trị (scope = admin) thì chỉ lấy sản phẩm đang hiển thị
        if (($filters['scope'] ?? null) !== 'admin') {
            $query->where('is_active', true);
        } elseif (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        // Lọc theo nhóm danh mục ('shoe' hoặc 'accessory')
        if (! empty($filters['group'])) {
            $group = $filters['group'];
            $query->whereHas('category', fn ($q) => $q->where('group_type', $group));
        }

        // Lọc theo danh mục cụ thể (id hoặc slug)
        if (! empty($filters['category'])) {
            $category = $filters['category'];
            $query->whereHas('category', function ($q) use ($category) {
                is_numeric($category)
                    ? $q->where('id', (int) $category)
                    : $q->where('slug', $category);
            });
        }

        // Tìm kiếm theo tên hoặc SKU (dùng cho Admin)
        if (! empty($filters['keyword'])) {
            $kw = trim((string) $filters['keyword']);
            $query->where(function ($q) use ($kw) {
                $q->where('name', 'like', "%{$kw}%")
                  ->orWhere('sku', 'like', "%{$kw}%");
            });
        }

        // Lọc theo giới tính (male, female, unisex)
        if (! empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        // Lọc theo size còn tồn kho (> 0)
        if (! empty($filters['size'])) {
            $size = (string) $filters['size'];
            $query->whereHas('variants', function ($q) use ($size) {
                $q->where('size', $size)
                  ->where('stock_quantity', '>', 0);
            });
        }

        // Sắp xếp / lọc giảm giá (mặc định bán chạy nhất lên trước)
        $sort = $filters['sort'] ?? 'default';

        if ($sort === 'sale') {
            $query->whereNotNull('sale_price')
                  ->where('sale_price', '>', 0)
                  ->whereColumn('sale_price', '<', 'base_price')
                  ->orderByDesc('sold_count')
                  ->orderByDesc('id');
        } elseif ($sort === 'price_asc') {
            $query->orderByRaw('COALESCE(NULLIF(sale_price, 0), base_price) ASC')
                  ->orderByDesc('id');
        } elseif ($sort === 'price_desc') {
            $query->orderByRaw('COALESCE(NULLIF(sale_price, 0), base_price) DESC')
                  ->orderByDesc('id');
        } else {
            $query->orderByDesc('sold_count')
                  ->orderByDesc('id');
        }

        return $query->paginate((int) ($filters['per_page'] ?? 12))->withQueryString();
    }

    /**
     * Lấy danh sách các size giày hiện đang còn hàng (stock_quantity > 0) để hiển thị ở bộ lọc.
     */
    public function getInStockShoeSizes(): array
    {
        $sizes = \App\Models\ProductVariant::query()
            ->where('stock_quantity', '>', 0)
            ->whereHas('product', function ($q) {
                $q->where('is_active', true)
                  ->whereHas('category', fn ($c) => $c->where('group_type', 'shoe'));
            })
            ->distinct()
            ->pluck('size')
            ->filter()
            ->values()
            ->all();

        natsort($sizes);

        return array_values($sizes);
    }

    /**
     * Ứng viên cho Chatbot (Seq02): đúng size, trong khoảng giá (theo giá bán thực tế), còn hàng, bán chạy trước, tối đa $limit mẫu.
     */
    public function getProductsBySizeAndPrice(string $size, ?int $priceMin, ?int $priceMax, int $limit = 10): Collection
    {
        // TODO: Product::active()->inGroup(Shoe)->hasSizeInStock($size) + lọc giá + orderByDesc('sold_count')->limit($limit)
    }

    /**
     * Tồn kho hiện tại của một biến thể (Seq03).
     */
    public function checkStock(int $variantId): int
    {
        // TODO: ProductVariant::whereKey($variantId)->value('stock_quantity')
    }

    /**
     * Trừ kho khi đặt hàng (Seq04). Dùng lockForUpdate và điều kiện stock_quantity >= $qty.
     */
    public function deductStock(int $variantId, int $qty): void
    {
        // TODO: không đủ -> throw InsufficientException
    }

    /**
     * Hoàn kho khi hủy đơn (Seq05).
     */
    public function restoreStock(int $variantId, int $qty): void
    {
        // TODO: increment('stock_quantity', $qty)
    }

    /**
     * Lưu sản phẩm và các size (Seq06). Transaction do Service mở.
     */
    public function saveProductAndVariants(array $productData, array $variants, ?int $productId = null): Product
    {
        // TODO: updateOrCreate sản phẩm, đồng bộ product_variants và product_images
    }

    /**
     * Sản phẩm bán chạy nhất (trang chủ, thống kê).
     */
    public function getBestSellers(int $limit = 8): Collection
    {
        // TODO: Product::active()->orderByDesc('sold_count')->limit($limit)->get()
    }

    /**
     * Sản phẩm mới nhất (trang chủ) - thêm mới: hàm chưa có trong Class Diagram.
     */
    public function getNewest(int $limit = 8): Collection
    {
        // TODO: orderByDesc('created_at')
    }

    /**
     * Sản phẩm liên quan (ý tưởng 4.3) - thêm mới: hàm chưa có trong Class Diagram. Gợi ý: cùng danh mục, khác id.
     */
    public function getRelated(Product $product, int $limit = 4): Collection
    {
        // TODO: viết truy vấn
    }

    /**
     * Admin cập nhật tồn kho theo size.
     */
    public function updateStock(int $productId, array $stocks): void
    {
        // TODO: cập nhật product_variants.stock_quantity
    }

    /**
     * Xóa mềm sản phẩm.
     */
    public function softDelete(int $id): void
    {
        // TODO: Product::findOrFail($id)->delete()
    }
}
