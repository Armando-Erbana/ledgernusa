<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAssistantController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'conversation_id' => 'nullable|string',
        ]);

        $apiKey = env('GEMINI_API_KEY');
        $userMessage = $request->input('message');

        $systemPrompt = "Anda adalah Asisten LedgerNusa, aplikasi akuntansi online Indonesia. "
                      . "Jawablah pertanyaan pengguna dengan ramah, singkat, dan menggunakan bahasa Indonesia yang baik. "
                      . "Jangan gunakan format markdown yang rumit. Jika memberikan langkah-langkah, gunakan angka (1, 2, 3). "
                      . "Fokus pada topik akuntansi, jurnal, laporan keuangan, dan cara penggunaan aplikasi LedgerNusa.";

        try {
            // Gunakan Http::retry() untuk mencoba ulang 3x jika Google Error 503/429
            $response = Http::withoutVerifying()
                ->retry(3, 1000, function ($exception) {
                    return $exception instanceof \Illuminate\Http\Client\RequestException && in_array($exception->response->status(), [503, 429, 500]);
                })
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}", [
                    'system_instruction' => [
                        'parts' => [['text' => $systemPrompt]]
                    ],
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [['text' => $userMessage]]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 2048, // <-- Dinaikkan agar teks tidak terpotong
                    ]
                ]);

            if ($response->failed()) {
                Log::error('Gemini API Error: ' . $response->body());
                throw new \Exception('API Error');
            }

            $data = $response->json();
            $answer = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak bisa memproses jawaban saat ini.';

            return response()->json([
                'answer' => trim($answer),
                'conversation_id' => $request->conversation_id ?? uniqid(),
            ]);

        } catch (\Exception $e) {
            Log::error('AI Chat Error: ' . $e->getMessage());
            
            // Tampilkan pesan yang ramah, BUKAN error 503
            return response()->json([
                'answer' => "Maaf, asisten AI sedang sibuk atau kehilangan koneksi. Silakan coba beberapa saat lagi ya.",
            ]);
        }
    }
}