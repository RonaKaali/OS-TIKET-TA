<?php

namespace App\Services;

use App\Models\ChatbotResponse;
use Illuminate\Support\Str;

class ChatbotService
{
    /**
     * Mencari response yang cocok berdasarkan pesan user
     *
     * @param string $message Pesan dari user
     * @return ChatbotResponse|null Response yang cocok atau null jika tidak ada
     */
    public function findResponse(string $message): ?ChatbotResponse
    {
        // Normalisasi pesan (lowercase, trim)
        $normalizedMessage = Str::lower(trim($message));

        // Ambil semua response aktif, diurutkan berdasarkan priority
        $responses = ChatbotResponse::active()
            ->ordered()
            ->get();

        // Loop melalui semua response untuk mencari yang cocok
        foreach ($responses as $response) {
            $keyword = Str::lower(trim($response->keyword));

            // Cocokkan berdasarkan match_type
            $isMatch = match ($response->match_type) {
                'exact' => $normalizedMessage === $keyword,
                'starts_with' => Str::startsWith($normalizedMessage, $keyword),
                'contains' => Str::contains($normalizedMessage, $keyword),
                default => Str::contains($normalizedMessage, $keyword),
            };

            if ($isMatch) {
                return $response;
            }
        }

        // Jika tidak ada yang cocok, kembalikan null
        return null;
    }

    /**
     * Mendapatkan response untuk pesan user
     * Jika tidak ada yang cocok, kembalikan response default
     *
     * @param string $message Pesan dari user
     * @return string Response text
     */
    public function getResponse(string $message): string
    {
        $response = $this->findResponse($message);

        if ($response) {
            return $response->response;
        }

        // Default response jika tidak ada yang cocok
        return "Maaf, saya tidak mengerti pertanyaan Anda. 😔\n\nSilakan coba salah satu dari:\n- Ketik /help untuk melihat daftar perintah\n- Ketik \"cara buat tiket\" untuk panduan\n- Ketik \"bantuan\" untuk bantuan\n- Hubungi admin untuk bantuan lebih lanjut\n\nTerima kasih!";
    }
}

