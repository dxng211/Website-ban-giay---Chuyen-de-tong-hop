<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\ProductRepository;
use App\Services\Clients\AiApiClient;
use Illuminate\Support\Collection;

/**
 * Phụ thuộc: AiApiClient, ProductRepository, SizeChart và ChatbotLog (Model, truy cập trực tiếp).
 * Nguyên tắc: code quy đổi size và lọc sản phẩm; Gemini chỉ chọn 2-3 mẫu trong danh sách đã lọc và viết lời tư vấn.
 */
class AiService
{
    public function __construct(
        private readonly AiApiClient $aiClient,
        private readonly ProductRepository $products,
    ) {}

    /**
     * Tư vấn size và gợi ý sản phẩm (UC08, Seq02).
     */
    public function suggest(array $data, ?User $user, string $sessionToken): array
    {
        // TODO: tra size: SizeChart::where('chart_key', 'shoe_vn')->coveringValue($footLength)->first(); không có -> trả Notice (chân ngoài bảng)
        // TODO: $this->products->getProductsBySizeAndPrice(size, giá min, giá max); rỗng -> trả size kèm Notice (không gọi Gemini)
        // TODO: $this->aiClient->generateProductSuggestionJson(need, ứng viên); lỗi -> throw AiServiceException
        // TODO: kiểm tra id Gemini chọn thuộc danh sách ứng viên (loại id lạ); ChatbotLog::create(...) ghi lịch sử
        // TODO: return ['suggested_size' => ..., 'products' => ..., 'ai_reply' => ...]
    }

    /**
     * Lịch sử tư vấn của người dùng (hoặc của phiên Guest).
     */
    public function listMine(?User $user, string $sessionToken): Collection
    {
        // TODO: ChatbotLog::where(...)->latest()->get()
    }
}
