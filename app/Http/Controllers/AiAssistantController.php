<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\AiChatHistory;
use App\Models\User;
use Carbon\Carbon;

class AiAssistantController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $sessionId = $request->get('session_id', $user->getCurrentChatSession());

        // Ambil history chat untuk session ini (maksimal 50 pesan terakhir)
        $chatHistory = $user->aiChatHistories()
            ->forSession($sessionId)
            ->orderBy('created_at', 'asc')
            ->limit(50)
            ->get();

        return view('pages.aiassistant.aiassistant', compact('chatHistory', 'sessionId'));
    }

    public function ask(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:2000',
            'session_id' => 'nullable|string'
        ]);

        $apiKey = config('services.gemini.api_key');

        if (!$apiKey) {
            return response()->json(['error' => 'API Key untuk AI tidak dikonfigurasi.'], 500);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userQuestion = $request->input('question');
        $sessionId = $request->input('session_id', $user->getCurrentChatSession());

        if (rand(1, 10) === 1) {
            $user->cleanupOldChats();
        }

        $userMessage = AiChatHistory::create([
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'message_type' => 'user',
            'message' => $userQuestion,
            'metadata' => [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]
        ]);

        $recentHistory = AiChatHistory::forUser($user->id)
            ->forSession($sessionId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->reverse();

        $contents = [
            ['role' => 'user', 'parts' => [['text' => "Anda adalah asisten AI yang ramah dan membantu di Learning Management System (LMS) Bosowa. Nama user adalah {$user->name}. Selalu jawab pertanyaan dengan sopan dan jelas."]]]
        ];

        foreach ($recentHistory as $history) {
            if ($history->id !== $userMessage->id) {
                $role = $history->message_type === 'user' ? 'user' : 'model';
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => $history->message]]
                ];
            }
        }

        $contents[] = ['role' => 'user', 'parts' => [['text' => $userQuestion]]];

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";
        $payload = ['contents' => $contents];

        try {
            $response = Http::timeout(30)->post($url, $payload);

            if ($response->successful() && isset($response->json()['candidates'][0]['content']['parts'][0]['text'])) {
                $answer = $response->json()['candidates'][0]['content']['parts'][0]['text'];

                AiChatHistory::create([
                    'user_id' => $user->id,
                    'session_id' => $sessionId,
                    'message_type' => 'ai',
                    'message' => $answer,
                    'metadata' => [
                        'response_time' => $response->transferStats?->getTransferTime(),
                        'model_used' => 'gemini-2.5-flash'
                    ]
                ]);

                return response()->json([
                    'answer' => $answer,
                    'session_id' => $sessionId,
                    'timestamp' => now()->format('H:i')
                ]);
            }

            Log::error('Gemini API Error:', $response->json());
            return response()->json([
                'error' => 'Maaf, terjadi kesalahan saat menghubungi asisten AI.',
                'details' => $response->json()
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('AI Assistant Error:', [
                'message' => $e->getMessage(),
                'user_id' => $user->id,
                'session_id' => $sessionId
            ]);

            return response()->json([
                'error' => 'Maaf, terjadi kesalahan koneksi. Silakan coba lagi.'
            ], 500);
        }
    }

    public function getHistory(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return response()->json(['error' => 'Session ID required'], 400);
        }

        $history = $user->aiChatHistories()
            ->forSession($sessionId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($chat) {
                return [
                    'id' => $chat->id,
                    'message_type' => $chat->message_type,
                    'message' => $chat->message,
                    'timestamp' => $chat->created_at->format('H:i'),
                    'formatted_time' => $chat->getFormattedTimeAttribute()
                ];
            });

        return response()->json(['history' => $history]);
    }

    public function clearHistory(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $sessionId = $request->input('session_id');

        if ($sessionId) {
            $user->aiChatHistories()->forSession($sessionId)->delete();
        } else {
            $user->aiChatHistories()->delete();
        }

        return response()->json(['message' => 'Chat history cleared successfully']);
    }

    public function newSession()
    {
        $sessionId = AiChatHistory::generateSessionId();
        return response()->json([
            'session_id' => $sessionId,
            'message' => 'New chat session created'
        ]);
    }
}
