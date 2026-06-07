<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatbotController extends Controller
{
    protected ChatbotService $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /**
     * Handle chatbot message request
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function message(Request $request): JsonResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $userMessage = $request->input('message');
        $result = $this->chatbotService->respond($userMessage);

        return response()->json([
            'success' => true,
            'response' => $result['response'],
            'suggestions' => $result['suggestions'] ?? [],
            'actions' => $result['actions'] ?? [],
        ]);
    }
}

