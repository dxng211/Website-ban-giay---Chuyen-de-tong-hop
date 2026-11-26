<?php

namespace App\Repositories;

use App\Enums\CategoryGroupType;
use App\Models\Product;
use App\Models\ProductVariant;
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
        return Product::with(['images', 'variants', 'category'])->find($id);
    }

    /**
     * Danh sách có lọc (giá, giảm giá, giới tính, size, loại) và phân trang. Mặc định bán chạy nhất lên trước.
     */
    public function list(array $filters = []): LengthAwarePaginator
    {
        $groupEnum = null;
        if (!empty($filters['group'])) {
            $groupEnum = $filters['group'] instanceof CategoryGroupType
                ? $filters['group']
                : CategoryGroupType::tryFrom((string) $filters['group']);
        }

        $query = Product::query()
            ->when(($filters['scope'] ?? null) !== 'admin', fn ($q) => $q->active())
            ->when($groupEnum !== null, fn ($q) => $q->inGroup($groupEnum))
            ->when(!empty($filters['category_id']), fn ($q) => $q->where('category_id', $filters['category_id']))
            ->when(!empty($filters['gender']), fn ($q) => $q->where('gender', $filters['gender']))
            ->when(!empty($filters['on_sale']), fn ($q) => $q->onSale())
            ->when(!empty($filters['size']), fn ($q) => $q->hasSizeInStock($filters['size']))
            ->when(
                isset($filters['min_price']) && $filters['min_price'] !== '',
                fn ($q) => $q->where('base_price', '>=', (int) $filters['min_price'])
            )
            ->when(
                isset($filters['max_price']) && $filters['max_price'] !== '',
                fn ($q) => $q->where('base_price', '<=', (int) $filters['max_price'])
            )
            ->when(!empty($filters['search']) || !empty($filters['q']), function ($q) use ($filters) {
                $keyword = trim((string) ($filters['search'] ?? $filters['q']));
                $q->where(function ($sub) use ($keyword) {
                    $sub->where('name', 'like', "%{$keyword}%")
                        ->orWhere('sku', 'like', "%{$keyword}%");
                });
            });

        $sort = $filters['sort'] ?? (($filters['scope'] ?? null) === 'admin' ? 'newest' : 'bestseller');
        match ($sort) {
            'price_asc' => $query->orderBy('base_price', 'asc'),
            'price_desc' => $query->orderBy('base_price', 'desc'),
            'newest' => $query->latest('id'),
            default => $query->orderByDesc('sold_count')->latest('id'),
        };

        $perPage = (int) ($filters['per_page'] ?? 12);

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Ứng viên cho Chatbot (Seq02): đúng size, trong khoảng giá (theo giá bán thực tế), còn hàng, bán chạy trước, tối đa $limit mẫu.
     */
    public function getProductsBySizeAndPrice(string $size, ?int $priceMin, ?int $priceMax, int $limit = 10): Collection
    {
        return Product::active()
            ->hasSizeInStock($size)
            ->when($priceMin !== null, fn ($q) => $q->whereRaw('COALESCE(sale_price, base_price) >= ?', [$priceMin]))
            ->when($priceMax !== null, fn ($q) => $q->whereRaw('COALESCE(sale_price, base_price) <= ?', [$priceMax]))
            ->orderByDesc('sold_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Tồn kho hiện tại của một biến thể (Seq03).
     */
    public function checkStock(int $variantId): int
    {
        return (int) (ProductVariant::whereKey($variantId)->value('stock_quantity') ?? 0);
    }

    /**
     * Trừ kho khi đặt hàng (Seq04). Dùng lockForUpdate và điều kiện stock_quantity >= $qty.
     */
    public function deductStock(int $variantId, int $qty): void
    {
        $updated = ProductVariant::whereKey($variantId)
            ->where('stock_quantity', '>=', $qty)
            ->lockForUpdate()
            ->decrement('stock_quantity', $qty);

        if ($updated === 0) {
            throw new \RuntimeException('Không đủ số lượng tồn kho cho biến thể sản phẩm.');
        }
    }

    /**
     * Hoàn kho khi hủy đơn (Seq05).
     */
    public function restoreStock(int $variantId, int $qty): void
    {
        ProductVariant::whereKey($variantId)->increment('stock_quantity', $qty);
    }

    /**
     * Lưu sản phẩm và các size (Seq06). Transaction do Service mở.
     */
    public function saveProductAndVariants(array $productData, array $variants, ?int $productId = null): Product
    {
        if (array_key_exists('price', $productData) && !array_key_exists('base_price', $productData)) {
            $productData['base_price'] = $productData['price'];
        }

        if (array_key_exists('is_active', $productData)) {
            $productData['is_active'] = filter_var($productData['is_active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true;
        }

        $images = $productData['images'] ?? null;
        unset($productData['price'], $productData['images'], $productData['image']);

        $fillableKeys = (new Product())->getFillable();
        $attributes = array_intersect_key($productData, array_flip($fillableKeys));

        /** @var Product $product */
        if ($productId !== null) {
            $product = Product::findOrFail($productId);
            $product->update($attributes);
            $product->refresh();
        } else {
            $product = Product::create($attributes);
        }

        if (!empty($variants)) {
            $keepIds = [];
            foreach ($variants as $index => $variant) {
                $size = trim((string) ($variant['size'] ?? 'Mặc định'));
                if ($size === '') {
                    continue;
                }

                $variantSku = trim((string) ($variant['variant_sku'] ?? $variant['sku'] ?? ''));
                if ($variantSku === '') {
                    $variantSku = ($product->sku ?? 'PRD') . '-' . strtoupper(preg_replace('/[^A-Za-z0-9]+/', '', $size)) . '-' . ($index + 1);
                }

                $record = $product->variants()->updateOrCreate(
                    ['size' => $size],
                    [
                        'variant_sku' => $variantSku,
                        'stock_quantity' => max(0, (int) ($variant['stock_quantity'] ?? 0)),
                    ]
                );
                $keepIds[] = $record->id;
            }

            if (!empty($keepIds)) {
                $product->variants()->whereNotIn('id', $keepIds)->delete();
            }
        }

        if (is_array($images) && !empty($images)) {
            $product->images()->delete();
            foreach ($images as $order => $path) {
                if (is_string($path) && $path !== '') {
                    $product->images()->create([
                        'image_url' => $path,
                        'sort_order' => (int) $order,
                    ]);
                }
            }
        }

        return $product->load(['images', 'variants']);
    }

    /**
     * Sản phẩm bán chạy nhất (trang chủ, thống kê).
     */
    public function getBestSellers(int $limit = 8): Collection
    {
        return Product::active()
            ->orderByDesc('sold_count')
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    /**
     * Sản phẩm mới nhất (trang chủ) - thêm mới: hàm chưa có trong Class Diagram.
     */
    public function getNewest(int $limit = 8): Collection
    {
        return Product::active()
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    /**
     * Sản phẩm liên quan (ý tưởng 4.3) - thêm mới: hàm chưa có trong Class Diagram. Gợi ý: cùng danh mục, khác id.
     */
    public function getRelated(Product $product, int $limit = 4): Collection
    {
        return Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    /**
     * Admin cập nhật tồn kho theo size.
     */
    public function updateStock(int $productId, array $stocks): void
    {
        $product = Product::findOrFail($productId);

        foreach ($stocks as $variantId => $qty) {
            $product->variants()
                ->where('id', (int) $variantId)
                ->update(['stock_quantity' => max(0, (int) $qty)]);
        }
    }

    /**
     * Xóa mềm sản phẩm.
     */
    public function softDelete(int $id): void
    {
        Product::findOrFail($id)->delete();
    }
}
