<?php

namespace App\Filament\Pages;

use App\Models\Conversation;
use App\Models\Message;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;

class Chat extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'WhatsApp';
    protected static string $view = 'filament.pages.chat';

    public ?int $selectedConversationId = null;
    public string $messageInput = '';

    public function mount()
    {
        $this->selectedConversationId = Conversation::latest()->first()?->id;
    }

    #[Computed]
    public function conversations()
    {
        return Conversation::with(['lead', 'messages' => fn($q) => $q->latest()->limit(1)])
            ->latest()
            ->get();
    }

    #[Computed]
    public function selectedConversation()
    {
        return Conversation::with(['lead', 'messages'])->find($this->selectedConversationId);
    }

    public function selectConversation($id)
    {
        $this->selectedConversationId = $id;
    }

    public function sendMessage()
    {
        if (empty($this->messageInput) || !$this->selectedConversationId) {
            return;
        }

        Message::create([
            'conversation_id' => $this->selectedConversationId,
            'content' => $this->messageInput,
            'direction' => 'outbound',
            'timestamp' => now(),
        ]);

        $this->messageInput = '';
    }
}
