<?php

namespace App\Http\Controllers;

use App\Models\AiConversation;
use App\Models\AiKnowledge;
use App\Models\AiMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAssistantController extends Controller
{
    public function chat(Request $request)
    {
        $data = $request->validate([
            'message' => 'required|string|max:1000',
            'conversation_id' => 'nullable|integer|exists:ai_conversations,id',
        ]);

        // Ambil / buat conversation
        $conversation = $data['conversation_id']
            ? AiConversation::where('id', $data['conversation_id'])
                ->where('user_id', auth()->id())
                ->first()
            : null;

        if (!$conversation) {
            $conversation = AiConversation::create([
                'user_id' => auth()->id(),
                'company_id' => session('company_id'),
                'title' => substr($data['message'], 0, 50),
            ]);
        }

        // Simpan pesan user
        AiMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $data['message'],
        ]);

        // LAPIS 1: Rule-based
        $answer = $this->matchKnowledge($data['message']);

        // LAPIS 2: Gemini (kalau rule-based tidak jawab)
        if (!$answer) {
            $answer = $this->askGemini($data['message'], $conversation);
        }

        // LAPIS 3: Fallback (kalau Gemini gagal juga)
        if (!$answer) {
            $answer = [
                'text' => "Maaf, saya belum bisa menjawab pertanyaan itu. 🙏\n\nCoba tanyakan hal lain seperti:\n• Cara input jurnal?\n• Apa itu COA?\n• Cara lihat laba rugi?\n• Cara install ke HP?",
                'action_url' => null,
                'action_label' => null,
            ];
        }

        // Simpan jawaban
        AiMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $answer['text'],
        ]);

        return response()->json([
            'conversation_id' => $conversation->id,
            'answer' => $answer['text'],
            'action_url' => $answer['action_url'] ?? null,
            'action_label' => $answer['action_label'] ?? null,
        ]);
    }

    /**
     * LAPIS 1: Cocokkan dengan knowledge base (FAQ pre-defined).
     */
    private function matchKnowledge(string $message): ?array
    {
        $lower = ' ' . strtolower($message) . ' ';
        $knowledges = AiKnowledge::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $best = null;
        $bestScore = 0;

        foreach ($knowledges as $k) {
            $keywords = $k->keywords ?? [];
            $score = 0;

            foreach ($keywords as $kw) {
                if (str_contains($lower, strtolower($kw))) {
                    $score++;
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $k;
            }
        }

        if ($best && $bestScore >= 1) {
            $best->increment('hits');
            return [
                'text' => $best->answer,
                'action_url' => $best->action_url,
                'action_label' => $best->action_label,
            ];
        }

        return null;
    }

    /**
     * LAPIS 2: Fallback ke Gemini dengan Membaca Manual Book.
     */
    private function askGemini(string $message, AiConversation $conversation): ?array
    {
        $apiKey = config('services.gemini.key');
        if (!$apiKey) {
            Log::warning('Gemini: API key kosong di config');
            return null;
        }

        // 1. BACA FILE MANUAL BOOK
        $manualPath = storage_path('app/manual/LedgerNusa_User_Manual.md');
        $manualContent = file_exists($manualPath) ? file_get_contents($manualPath) : 'Manual book belum tersedia.';

        // Ambil riwayat chat untuk konteks (maks 6 pesan terakhir)
        $history = AiMessage::where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get()
            ->reverse()
            ->map(function ($msg) {
                return [
                    'role' => $msg->role === 'user' ? 'user' : 'model',
                    'parts' => [['text' => $msg->content]],
                ];
            })
            ->values()
            ->toArray();

        // Pastikan pesan terakhir adalah pesan user
        $contents = $history;
        if (empty($contents) || end($contents)['role'] !== 'user') {
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $message]],
            ];
        }

        $company = \App\Models\Company::find(session('company_id'));
        $user = auth()->user();

        // 2. SUSUN SYSTEM PROMPT DENGAN MENYISIPKAN MANUAL BOOK
        $systemPrompt = "Kamu adalah Asisten LedgerNusa, asisten virtual untuk aplikasi akuntansi LedgerNusa yang membantu UMKM Indonesia.\n\n"
            . "GAYA JAWAB:\n"
            . "- Bahasa Indonesia santai, ramah, profesional\n"
            . "- Maksimal 4 kalimat per jawaban (kecuali kalau kasih langkah-langkah)\n"
            . "- Kalau user tanya 'cara X', kasih langkah bernomor\n"
            . "- Jangan pernah sebut dirimu sebagai AI dari Google, Gemini, atau platform lain\n"
            . "- Kalau tidak tahu, katakan dengan jujur\n\n"
            . "KONTEKS USER:\n"
            . "- Nama: {$user->name}\n"
            . "- Perusahaan: " . ($company->name ?? 'Belum ada') . "\n"
            . "- Role: " . ($user->roleInActiveCompany() ?? '-') . "\n\n"
            . "ATURAN PENTING:\n"
            . "1. Gunakan dokumen manual book di bawah ini sebagai referensi UTAMA untuk menjawab pertanyaan tentang cara pakai aplikasi.\n"
            . "2. Jika pertanyaan seputar akuntansi umum, jawab dengan pengetahuanmu.\n"
            . "3. Jangan mengarang fitur yang tidak ada di manual.\n"
            . "4. Jangan pernah menyebut 'Gemini', 'Google', 'AI saya' — kamu adalah Asisten LedgerNusa.\n\n"
            . "=== DOKUMEN MANUAL BOOK LEDGERNUSA ===\n"
            . $manualContent . "\n"
            . "=====================================\n";

        try {
            $response = Http::timeout(20)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                    'system_instruction' => [
                        'parts' => [['text' => $systemPrompt]],
                    ],
                    'contents' => $contents,
                    'generationConfig' => [
                        'temperature' => 0.5, // Diturunkan sedikit agar AI lebih patuh pada manual
                        'maxOutputTokens' => 800, // Dinaikkan karena jawaban langkah-langkah butuh token lebih banyak
                        'topP' => 0.95,
                    ],
                ]);

            if (!$response->successful()) {
                Log::warning('Gemini API error: ' . $response->status() . ' - ' . substr($response->body(), 0, 500));
                return null;
            }

            $text = $response->json('candidates.0.content.parts.0.text');

            if (!$text) {
                Log::warning('Gemini empty response: ' . substr($response->body(), 0, 500));
                return null;
            }

            return [
                'text' => trim($text),
                'action_url' => null,
                'action_label' => null,
            ];
        } catch (\Exception $e) {
            Log::error('Gemini exception: ' . $e->getMessage());
            return null;
        }
    }
}