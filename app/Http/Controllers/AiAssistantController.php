<?php

namespace App\Http\Controllers;

use App\Models\AiConversation;
use App\Models\AiKnowledge;
use App\Models\AiMessage;
use Illuminate\Http\Request;

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

        // Cari di knowledge base
        $answer = $this->matchKnowledge($data['message']);

        if (!$answer) {
            $answer = [
                'text' => "Maaf, saya belum punya jawaban untuk pertanyaan itu. \n\nCoba tanyakan hal lain seperti:\n• Cara input jurnal?\n• Apa itu COA?\n• Cara lihat laba rugi?\n• Cara install ke HP?",
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

        // Minimal 1 keyword cocok
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
}