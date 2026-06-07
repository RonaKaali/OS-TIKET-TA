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

        try {
            $userMessage = trim((string) $request->input('message'));
            $result = $this->chatbotService->respond($userMessage);

            return response()->json([
                'success' => true,
                'response' => $result['response'] ?? '',
                'suggestions' => $result['suggestions'] ?? [],
                'actions' => $result['actions'] ?? [],
            ]);
        } catch (\Throwable $e) {
            \Log::error('Chatbot message failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => true,
                'response' => "🤖 **Asisten CSIRT**\n\nMaaf, layanan sementara bermasalah. Silakan coba lagi atau hubungi **csirt@kalselprov.go.id**.\n\n**Bantuan cepat:**\n- Ketik *cara lapor* untuk panduan melapor\n- Ketik *status laporan* untuk lacak tiket\n- Ketik *phishing* atau *malware* untuk solusi insiden",
                'suggestions' => ['Cara lapor insiden', 'Cek status laporan', 'Phishing', 'Kontak CSIRT'],
                'actions' => [],
            ]);
        }
    }
}

