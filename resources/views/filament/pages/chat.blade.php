<x-filament-panels::page class="h-[calc(100vh-8rem)]">
    <div class="flex h-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden dark:bg-gray-900 dark:border-gray-800">
        <!-- Sidebar -->
        <div class="w-1/3 border-r border-gray-200 dark:border-gray-800 flex flex-col">
            <!-- Search -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-800">
                <div class="relative">
                    <input type="text" placeholder="Search chats..." class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-primary-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" />
                </div>
            </div>

            <!-- Conversations List -->
            <div class="flex-1 overflow-y-auto">
                @foreach($this->conversations as $conversation)
                    <div 
                        wire:click="selectConversation({{ $conversation->id }})"
                        class="p-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50 transition dark:border-gray-800 dark:hover:bg-gray-800 {{ $selectedConversationId === $conversation->id ? 'bg-blue-50 dark:bg-gray-800' : '' }}"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-lg">
                                {{ substr($conversation->lead->name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-baseline">
                                    <h3 class="font-semibold text-gray-900 truncate dark:text-white">{{ $conversation->lead->name }}</h3>
                                    <span class="text-xs text-gray-500">{{ $conversation->updated_at->format('H:i') }}</span>
                                </div>
                                <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                    {{ $conversation->messages->first()?->content ?? 'No messages yet' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 flex flex-col bg-gray-50 dark:bg-gray-950">
            @if($this->selectedConversation)
                <!-- Header -->
                <div class="p-4 bg-white border-b border-gray-200 flex justify-between items-center dark:bg-gray-900 dark:border-gray-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold">
                            {{ substr($this->selectedConversation->lead->name, 0, 1) }}
                        </div>
                        <div>
                            <h2 class="font-semibold text-gray-900 dark:text-white">{{ $this->selectedConversation->lead->name }}</h2>
                            <span class="text-xs text-green-500 flex items-center gap-1">
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span> Online
                            </span>
                        </div>
                    </div>
                    <div class="flex gap-2 text-gray-400">
                        <x-heroicon-o-magnifying-glass class="w-6 h-6 cursor-pointer hover:text-gray-600" />
                        <x-heroicon-o-ellipsis-vertical class="w-6 h-6 cursor-pointer hover:text-gray-600" />
                    </div>
                </div>

                <!-- Messages -->
                <div class="flex-1 overflow-y-auto p-4 space-y-4">
                    @foreach($this->selectedConversation->messages as $message)
                        <div class="flex {{ $message->direction === 'outbound' ? 'justify-end' : 'justify-start' }}">
                            @if($message->direction !== 'outbound')
                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold mr-2 self-end">
                                    {{ substr($this->selectedConversation->lead->name, 0, 1) }}
                                </div>
                            @endif
                            
                            <div class="max-w-[70%] rounded-2xl p-3 {{ $message->direction === 'outbound' ? 'bg-blue-100 text-gray-900 rounded-br-none' : 'bg-white text-gray-900 rounded-bl-none shadow-sm' }}">
                                <p>{{ $message->content }}</p>
                                <div class="text-xs text-gray-400 text-right mt-1 flex justify-end items-center gap-1">
                                    {{ $message->created_at->format('H:i A') }}
                                    @if($message->direction === 'outbound')
                                        <x-heroicon-m-check class="w-3 h-3 text-blue-500" />
                                    @endif
                                </div>
                            </div>

                            @if($message->direction === 'outbound')
                                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold ml-2 self-end">
                                    You
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Input -->
                <div class="p-4 bg-white border-t border-gray-200 dark:bg-gray-900 dark:border-gray-800">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-face-smile class="w-6 h-6 text-gray-400 cursor-pointer hover:text-gray-600" />
                        <x-heroicon-o-paper-clip class="w-6 h-6 text-gray-400 cursor-pointer hover:text-gray-600" />
                        
                        <div class="flex-1 relative">
                            <input 
                                wire:model="messageInput" 
                                wire:keydown.enter="sendMessage"
                                type="text" 
                                placeholder="Type a message..." 
                                class="w-full bg-gray-100 border-0 rounded-full px-4 py-2 focus:ring-0 dark:bg-gray-800 dark:text-white"
                            >
                        </div>

                        <button wire:click="sendMessage" class="p-2 bg-blue-600 rounded-full text-white hover:bg-blue-700 transition">
                            <x-heroicon-m-paper-airplane class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            @else
                <div class="flex-1 flex items-center justify-center text-gray-400">
                    Select a conversation to start chatting
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
