<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatbotRequest;
use App\Services\AiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiController extends Controller
{
    //
    public function __construct(
        private readonly AiService $aiService,
    ) {}

    /**
     * POST /chatbot/recommend (Seq02, throttle:10,1)
     */
    public function suggest(ChatbotRequest $request): JsonResponse
    {
        // TODO: gọi $this->aiService->suggest($request->validated(), Auth::user(), session()->getId())
        // TODO: bắt AiServiceException -> trả JSON {error: ...} (Hệ thống AI đang bận)
    }

    /**
     * GET /chatbot/history
     */
    public function listMine(Request $request): JsonResponse
    {
        // TODO: gọi $this->aiService->listMine(Auth::user(), session()->getId())
    }
}
