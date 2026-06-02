@extends('layouts.app', [
    'title' => 'Chat - Disty Teknologi',
    'pageLabel' => 'Chat'
])

@section('content')
    <div class="chat-layout">
        <aside class="conversation-panel">
            <div class="conversation-filter">
                <span>Sort by</span>
                <select>
                    <option>New</option>
                    <option>Waiting CS</option>
                    <option>Active</option>
                </select>
            </div>

            <div class="conversation-list">
                @forelse ($conversations as $conversation)
                    <a href="{{ route('chat.show', $conversation) }}"
                       class="conversation-card {{ optional($selectedConversation)->conversation_id === $conversation->conversation_id ? 'selected' : '' }}">

                        <div class="conversation-phone">
                            {{ $conversation->customer->phone ?? 'No phone' }}
                        </div>

                        <div class="conversation-meta">
                            <span class="status-pill {{ $conversation->current_status }}">
                                {{ str_replace('_', ' ', $conversation->current_status) }}
                            </span>

                            @if ($conversation->current_status === 'waiting_cs')
                                <span class="red-dot"></span>
                            @endif
                        </div>

                        <div class="conversation-footer">
                            <span>{{ $conversation->messages_count }} messages</span>
                            <span>{{ $conversation->assignedCs->full_name ?? 'Unassigned' }}</span>
                        </div>
                    </a>
                @empty
                    <p class="empty-small">No conversations found.</p>
                @endforelse
            </div>
        </aside>

        <section class="chat-window">
            @if ($selectedConversation)
                <div class="chat-header">
                    <div>
                        <h2>{{ $selectedConversation->customer->full_name ?? 'Unknown Customer' }}</h2>
                        <p>{{ $selectedConversation->customer->phone ?? '-' }}</p>
                    </div>

                    <span class="status-pill {{ $selectedConversation->current_status }}">
                        {{ str_replace('_', ' ', $selectedConversation->current_status) }}
                    </span>
                </div>

                <div class="message-list">
                    @foreach ($selectedConversation->messages as $message)
                        @php
                            $isStaff = in_array(optional($message->sender)->role, ['cs', 'admin']);
                        @endphp

                        <div class="message-row {{ $isStaff ? 'outgoing' : 'incoming' }}">
                            <div class="message-bubble">
                                <p>{{ $message->content }}</p>
                                <small>
                                    {{ $message->sender->full_name ?? 'System' }}
                                    ·
                                    {{ optional($message->created_at)->format('H:i') }}
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('chat.send', $selectedConversation) }}" class="message-form">
                    @csrf
                    <input type="text" name="content" placeholder="Type a message..." required>
                    <button type="submit">Send</button>
                </form>
            @else
                <div class="chat-empty">
                    <h1>Select a conversation to get started</h1>
                    <p>Choose chat from the list on the left to view messages and reply to customers</p>
                </div>
            @endif
        </section>
    </div>
@endsection