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

        $apiKey = env('GROQ_API_KEY');
        $userMessage = $request->input('message');

        $systemPrompt = "Anda adalah Asisten LedgerNusa, aplikasi akuntansi online Indonesia. "
                      . "Jawablah pertanyaan pengguna dengan ramah, singkat, dan menggunakan bahasa Indonesia yang baik. "
                      . "Jangan gunakan format markdown yang rumit. Jika memberikan langkah-langkah, gunakan angka (1, 2, 3). "
                      . "Fokus pada topik akuntansi, jurnal, laporan keuangan, dan cara penggunaan aplikasi LedgerNusa.";

        try {
            $response = Http::withToken($apiKey)->withoutVerifying()
                ->retry(3, 1000, function ($exception) {
                    return $exception instanceof \Illuminate\Http\Client\RequestException 
                        && in_array($exception->response->status(), [500, 502, 503, 504]);
                })
                ->timeout(20)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'openai/gpt-oss-120b',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userMessage],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 4096,
                ]);

            if ($response->status() === 429) {
                return response()->json([
                    'answer' => "Mohon maaf, kuota asisten AI gratis sedang penuh. Silakan tunggu 1 menit dan coba lagi ya. 🙏",
                    'conversation_id' => $request->conversation_id ?? uniqid(),
                ]);
            }

            if ($response->failed()) {
                Log::error('Groq API Error: ' . $response->body());
                throw new \Exception('Gagal menghubungi AI Groq: ' . $response->status());
            }

            $data = $response->json();
            $answer = $data['choices'][0]['message']['content'] ?? 'Maaf, saya tidak bisa memproses jawaban saat ini.';

            return response()->json([
                'answer' => trim($answer),
                'conversation_id' => $request->conversation_id ?? uniqid(),
            ]);

        } catch (\Exception $e) {
            Log::error('AI Chat Exception: ' . $e->getMessage());
            
            return response()->json([
                'answer' => "Maaf, asisten AI sedang sibuk atau kehilangan koneksi. Silakan coba beberapa saat lagi ya.",
                'conversation_id' => $request->conversation_id ?? uniqid(),
            ]);
        }
    }
}