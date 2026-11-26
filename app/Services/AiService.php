<?php

namespace App\Services;

use App\Exceptions\AiServiceException;
use App\Models\ChatbotLog;
use App\Models\SizeChart;
use App\Models\User;
use App\Repositories\ProductRepository;
use App\Services\Clients\AiApiClient;
use Illuminate\Support\Collection;
use Throwable;

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
        $footLength = (float) ($data['foot_length'] ?? $data['foot_length_cm'] ?? 0);
        $minPrice = isset($data['min_price']) && $data['min_price'] !== '' ? (int) $data['min_price'] : null;
        $maxPrice = isset($data['max_price']) && $data['max_price'] !== '' ? (int) $data['max_price'] : null;
        $need = (string) ($data['need'] ?? $data['note'] ?? '');

        $chart = SizeChart::query()
            ->where('chart_key', 'shoe_vn')
            ->coveringValue($footLength)
            ->first();

        if (!$chart) {
            $notice = 'Chiều dài chân nằm ngoài bảng quy đổi kích cỡ hiện tại.';

            ChatbotLog::create([
                'user_id' => $user?->id,
                'session_token' => $sessionToken,
                'foot_length' => $footLength,
                'suggested_size' => null,
                'notice' => $notice,
                'ai_reply' => $notice,
                'recommended_product_ids' => [],
            ]);

            return [
                'suggested_size' => null,
                'products' => collect(),
                'ai_reply' => $notice,
                'notice' => $notice,
            ];
        }

        $suggestedSize = (string) ($chart->size_label ?? $chart->size ?? '');
        $candidates = $this->products->getProductsBySizeAndPrice($suggestedSize, $minPrice, $maxPrice);

        if ($candidates->isEmpty()) {
            $notice = "Gợi ý size {$suggestedSize}, nhưng hiện chưa có sản phẩm phù hợp với khoảng giá này.";

            ChatbotLog::create([
                'user_id' => $user?->id,
                'session_token' => $sessionToken,
                'foot_length' => $footLength,
                'suggested_size' => $suggestedSize,
                'notice' => $notice,
                'ai_reply' => $notice,
                'recommended_product_ids' => [],
            ]);

            return [
                'suggested_size' => $suggestedSize,
                'products' => collect(),
                'ai_reply' => $notice,
                'notice' => $notice,
            ];
        }

        $candidateList = $candidates->values();
        $allowedIds = $candidateList->pluck('id')->map(fn ($id) => (int) $id)->all();

        try {
            $aiResult = $this->aiClient->generateProductSuggestionJson($need, $candidateList->all());
        } catch (Throwable $e) {
            throw new AiServiceException('Lỗi khi gọi dịch vụ tư vấn AI: ' . $e->getMessage(), 0, $e);
        }

        $rawIds = $aiResult['product_ids'] ?? $aiResult['recommended_product_ids'] ?? [];
        if (!is_array($rawIds)) {
            $rawIds = [];
        }

        $selectedIds = [];
        foreach ($rawIds as $id) {
            $intId = (int) $id;
            if (in_array($intId, $allowedIds, true) && !in_array($intId, $selectedIds, true)) {
                $selectedIds[] = $intId;
            }
            if (count($selectedIds) >= 3) {
                break;
            }
        }

        if (empty($selectedIds)) {
            $selectedIds = array_slice($allowedIds, 0, min(3, count($allowedIds)));
        }

        $recommendedProducts = $candidateList->whereIn('id', $selectedIds)->values();
        $aiReply = (string) ($aiResult['reply'] ?? $aiResult['ai_reply'] ?? "Gợi ý size {$suggestedSize} phù hợp với chiều dài chân của bạn.");

        ChatbotLog::create([
            'user_id' => $user?->id,
            'session_token' => $sessionToken,
            'foot_length' => $footLength,
            'suggested_size' => $suggestedSize,
            'notice' => null,
            'ai_reply' => $aiReply,
            'recommended_product_ids' => $selectedIds,
        ]);

        return [
            'suggested_size' => $suggestedSize,
            'products' => $recommendedProducts,
            'ai_reply' => $aiReply,
            'notice' => null,
        ];
    }

    /**
     * Lịch sử tư vấn của người dùng (hoặc của phiên Guest).
     */
    public function listMine(?User $user, string $sessionToken): Collection
    {
        return ChatbotLog::query()
            ->when(
                $user !== null,
                fn ($q) => $q->where('user_id', $user->id),
                fn ($q) => $q->where('session_token', $sessionToken)
            )
            ->latest()
            ->get();
    }
}
