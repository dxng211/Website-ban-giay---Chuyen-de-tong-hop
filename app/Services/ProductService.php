<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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
    public function getHomeData(): array
    {
        // TODO: ['bestSellers' => products->getBestSellers(n), 'newest' => products->getNewest(n)]
    }

    /**
     * Danh sách sản phẩm có lọc và phân trang (UC03). Mặc định sắp bán chạy nhất.
     */
    public function list(array $filters): LengthAwarePaginator
    {
        // TODO: chuyển $filters cho $this->products->list($filters)
    }

    /**
     * Chi tiết sản phẩm (UC04, Seq01), kèm sản phẩm liên quan.
     */
    public function getById(int $id): Product
    {
        // TODO: $this->products->findWithSizes($id); null -> throw NotFoundException
    }

    /**
     * Admin thêm sản phẩm (Seq06): lưu ảnh, mở Transaction, lưu sản phẩm và các size.
     */
    public function create(array $data): Product
    {
        // TODO: xử lý upload ảnh; DB::transaction(...) gọi $this->products->saveProductAndVariants(...); lỗi -> TransactionFailedException
    }

    /**
     * Admin sửa sản phẩm (Seq06).
     */
    public function update(int $id, array $data): Product
    {
        // TODO: tương tự create
    }

    /**
     * Admin cập nhật tồn kho theo size.
     */
    public function updateStock(int $id, array $stocks): void
    {
        // TODO: $this->products->updateStock($id, $stocks); số lượng không âm
    }

    /**
     * Admin xóa mềm sản phẩm (BR2-UC09).
     */
    public function close(int $id): void
    {
        // TODO: $this->products->softDelete($id)
    }
}
