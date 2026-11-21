<?php

namespace App\Services\Clients;

/**
 * Gọi Google Gemini API. Khóa API lưu trong .env, đọc qua config('services.gemini'). Chỉ gọi từ server.
 */
class AiApiClient
{
    /**
     * Gửi một lời nhắc, nhận văn bản.
     */
    public function generateText(string $prompt): string
    {
        // TODO: HTTP POST bằng Http::timeout(...); lỗi mạng, quá thời gian -> throw AiServiceException
    }

    /**
     * Gửi nhu cầu và danh sách ứng viên, yêu cầu Gemini chỉ chọn 2-3 id trong danh sách và trả JSON {product_ids: [...], advice: '...'}.
     */
    public function generateProductSuggestionJson(string $need, array $candidates): array
    {
        // TODO: JSON sai định dạng -> throw AiServiceException
    }
}
