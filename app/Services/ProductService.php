<?php

namespace App\Services;

use App\Exceptions\TransactionFailedException;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Phụ thuộc: ProductRepository.
 */
class ProductService
{
    public function __construct(
        private readonly ProductRepository $products,
    ) {}

    /**
     * Dữ liệu trang chủ (thêm mới: hàm chưa có trong Class Diagram).
     */
    public function getHomeData(int $limit = 8): array
    {
        return [
            'bestSellers' => $this->products->getBestSellers($limit),
            'newest' => $this->products->getNewest($limit),
        ];
    }

    /**
     * Danh sách sản phẩm có lọc và phân trang (UC03). Mặc định sắp bán chạy nhất.
     */
    public function list(array $filters = []): LengthAwarePaginator
    {
        return $this->products->list($filters);
    }

    /**
     * Chi tiết sản phẩm (UC04, Seq01), kèm sản phẩm liên quan.
     */
    public function getById(int $id): Product
    {
        $product = $this->products->findWithSizes($id);

        if (!$product) {
            throw (new ModelNotFoundException())->setModel(Product::class, [$id]);
        }

        return $product;
    }

    /**
     * Lấy danh sách danh mục phục vụ form tạo/sửa sản phẩm.
     */
    public function getFormCategories(): Collection
    {
        return Category::query()->orderBy('name')->get();
    }

    /**
     * Admin thêm sản phẩm (Seq06): lưu ảnh, mở Transaction, lưu sản phẩm và các size.
     */
    public function create(array $data): Product
    {
        $imagePath = $this->storeImage($data['image'] ?? null);

        try {
            return DB::transaction(function () use ($data, $imagePath) {
                $payload = $data;
                unset($payload['variants'], $payload['has_sizes'], $payload['stock_quantity']);

                if ($imagePath !== null) {
                    $payload['image'] = $imagePath;
                    $payload['images'] = [$imagePath];
                }

                $variants = $this->resolveVariants($data);

                return $this->products->saveProductAndVariants($payload, $variants);
            });
        } catch (Throwable $e) {
            $this->deleteImage($imagePath);
            throw new TransactionFailedException('Không thể tạo sản phẩm: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Admin sửa sản phẩm (Seq06).
     */
    public function update(int $id, array $data): Product
    {
        $existing = $this->getById($id);
        $oldImage = $existing->image ?? null;
        $newImagePath = $this->storeImage($data['image'] ?? null);

        try {
            $product = DB::transaction(function () use ($id, $data, $newImagePath, $oldImage) {
                $payload = $data;
                unset($payload['variants'], $payload['has_sizes'], $payload['stock_quantity']);

                $resolvedImage = $newImagePath ?? $oldImage;
                if ($resolvedImage !== null) {
                    $payload['image'] = $resolvedImage;
                    if ($newImagePath !== null) {
                        $payload['images'] = [$newImagePath];
                    }
                }

                $variants = $this->resolveVariants($data);

                return $this->products->saveProductAndVariants($payload, $variants, $id);
            });

            if ($newImagePath !== null && $oldImage && $oldImage !== $newImagePath) {
                $this->deleteImage($oldImage);
            }

            return $product;
        } catch (Throwable $e) {
            $this->deleteImage($newImagePath);
            throw new TransactionFailedException('Không thể cập nhật sản phẩm: ' . $e->getMessage(), 0, $e);
        }
    }

    private function resolveVariants(array $data): array
    {
        $hasSizes = array_key_exists('has_sizes', $data)
            ? filter_var($data['has_sizes'], FILTER_VALIDATE_BOOLEAN)
            : true;

        if (!$hasSizes) {
            return [[
                'size' => 'Mặc định',
                'sku' => ($data['sku'] ?? 'PRD') . '-DEFAULT',
                'stock_quantity' => max(0, (int) ($data['stock_quantity'] ?? 0)),
            ]];
        }

        if (!empty($data['variants']) && is_array($data['variants'])) {
            return array_values(array_filter(
                $data['variants'],
                fn ($v) => is_array($v) && trim((string) ($v['size'] ?? '')) !== ''
            ));
        }

        return [];
    }

    /**
     * Admin cập nhật tồn kho theo size.
     */
    public function updateStock(int $id, array $stocks): void
    {
        $this->getById($id);

        $sanitized = [];
        foreach ($stocks as $variantId => $qty) {
            $sanitized[$variantId] = max(0, (int) $qty);
        }

        $this->products->updateStock($id, $sanitized);
    }

    /**
     * Admin xóa mềm sản phẩm (BR2-UC09).
     */
    public function close(int $id): void
    {
        $this->getById($id);
        $this->products->softDelete($id);
    }

    private function storeImage(mixed $file): ?string
    {
        if (!$file instanceof UploadedFile) {
            return is_string($file) && $file !== '' ? $file : null;
        }

        return $file->store('products', 'public');
    }

    private function deleteImage(?string $path): void
    {
        if ($path && !str_starts_with($path, 'http') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
