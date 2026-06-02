<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index()
    {
        $conversations = $this->getConversationList();

        return view('chat.index', [
            'conversations' => $conversations,
            'selectedConversation' => null,
        ]);
    }

    public function show(Conversation $conversation)
    {
        $conversations = $this->getConversationList();

        $selectedConversation = $conversation->load([
            'customer',
            'assignedCs',
            'messages.sender',
            'messages.intent',
        ]);

        return view('chat.index', compact('conversations', 'selectedConversation'));
    }

    public function send(Request $request, Conversation $conversation)
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:5000'],
        ]);

        /*
         * Sementara pakai CS ID pertama.
         * Nanti wajib diganti dengan auth()->id() setelah login dibuat.
         */
        $csId = $conversation->assigned_cs_id ?? 1;

        DB::transaction(function () use ($validated, $conversation, $csId) {
            Message::create([
                'conversation_id' => $conversation->conversation_id,
                'sender_id' => $csId,
                'intent_id' => null,
                'content' => $validated['content'],
                'created_at' => now(),
            ]);

            $conversation->update([
                'current_status' => 'active',
            ]);
        });

        return redirect()
            ->route('chat.show', $conversation)
            ->with('success', 'Message sent.');
    }

    private function getConversationList()
    {
        return Conversation::query()
            ->with(['customer', 'assignedCs'])
            ->withCount('messages')
            ->whereIn('current_status', ['active', 'waiting_cs'])
            ->orderByRaw("FIELD(current_status, 'waiting_cs', 'active', 'closed')")
            ->orderByDesc('started_at')
            ->get();
    }
}
