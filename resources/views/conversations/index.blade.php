@extends('layouts.app')
@section('title', 'Messages')
@section('content')
<div class="container py-4">
    <h4 class="fw-bold mb-4"><i class="bi bi-chat-dots me-2"></i>Messages</h4>

    @if($conversations->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-chat-square-dots fs-1 d-block mb-3"></i>
            <h5>No conversations yet</h5>
            <p>Message a seller from any listing page.</p>
        </div>
    @else
        <div class="list-group">
            @foreach($conversations as $conv)
                @php $other = $conv->getOtherParticipant(auth()->user()); @endphp
                <a href="{{ route('conversations.show', $conv->id) }}"
                   class="list-group-item list-group-item-action d-flex gap-3 align-items-center py-3">
                    <img src="{{ $other->avatar_url }}" class="rounded-circle" width="44" height="44" style="object-fit:cover">
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold">{{ $other->name }}</span>
                            <span class="text-muted small">{{ $conv->last_message_at?->diffForHumans() }}</span>
                        </div>
                        <div class="text-muted small text-truncate">Re: {{ $conv->listing->title ?? 'Deleted listing' }}</div>
                        @if($conv->messages->isNotEmpty())
                            <div class="text-muted small text-truncate">
                                {{ Str::limit($conv->messages->first()->body, 60) }}
                            </div>
                        @endif
                    </div>
                    @php $unread = $conv->unreadCountFor(auth()->user()); @endphp
                    @if($unread > 0)
                        <span class="badge rounded-pill" style="background:var(--lasu-green)">{{ $unread }}</span>
                    @endif
                </a>
            @endforeach
        </div>
        {{ $conversations->links() }}
    @endif
</div>
@endsection
