@extends('layouts.app')
@section('title', 'Chat with ' . $other->name)
@section('content')
<div class="container py-4">
    <div class="row g-3">
        {{-- Chat panel --}}
        <div class="col-lg-8">
            <div class="card d-flex flex-column" style="height:620px">
                {{-- Header --}}
                <div class="card-header d-flex align-items-center gap-3 py-3" style="background:var(--lasu-green)">
                    <img src="{{ $other->avatar_url }}" class="rounded-circle" width="40" height="40" style="object-fit:cover">
                    <div class="text-white">
                        <div class="fw-bold">{{ $other->name }}</div>
                        <div class="small opacity-75">Re: {{ $conversation->listing->title ?? 'Listing' }}</div>
                    </div>
                </div>

                {{-- Messages --}}
                <div class="flex-grow-1 overflow-auto p-3" id="messagesBox" style="background:#f8f9fa">
                    @forelse($conversation->messages as $msg)
                        @php $mine = $msg->sender_id === auth()->id(); @endphp
                        <div class="d-flex {{ $mine ? 'justify-content-end' : 'justify-content-start' }} mb-3">
                            @if(!$mine)
                                <img src="{{ $msg->sender->avatar_url }}" class="rounded-circle me-2 align-self-end"
                                     width="28" height="28" style="object-fit:cover">
                            @endif
                            <div style="max-width:70%">
                                <div class="rounded-3 px-3 py-2 shadow-sm"
                                     style="background:{{ $mine ? 'var(--lasu-green)' : '#fff' }}; color:{{ $mine ? '#fff' : '#000' }}">
                                    @if($msg->attachment_path)
                                        <a href="{{ asset('storage/'.$msg->attachment_path) }}" target="_blank" class="{{ $mine ? 'text-white' : '' }}">
                                            <i class="bi bi-paperclip me-1"></i>Attachment
                                        </a>
                                    @endif
                                    {{ $msg->body }}
                                </div>
                                <div class="text-muted mt-1" style="font-size:.7rem;text-align:{{ $mine ? 'right' : 'left' }}">
                                    {{ $msg->created_at->format('h:i A') }}
                                    @if($mine && $msg->read_at) <i class="bi bi-check2-all"></i> @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5 small">
                            No messages yet. Start the conversation!
                        </div>
                    @endforelse
                </div>

                {{-- Input --}}
                <div class="card-footer p-3">
                    <form method="POST" action="{{ route('conversations.message', $conversation->id) }}"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="input-group">
                            <label class="btn btn-outline-secondary" title="Attach file">
                                <i class="bi bi-paperclip"></i>
                                <input type="file" name="attachment" class="d-none" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            </label>
                            <input type="text" name="body" class="form-control" placeholder="Type a message..."
                                   id="msgInput" autocomplete="off">
                            <button class="btn btn-lasu px-3"><i class="bi bi-send"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar: listing + meetup --}}
        <div class="col-lg-4">
            {{-- Listing card --}}
            @if($conversation->listing)
                <div class="card p-3 mb-3">
                    <div class="d-flex gap-2 align-items-start">
                        <img src="{{ $conversation->listing->primary_image_url }}"
                             style="width:56px;height:56px;object-fit:cover;border-radius:8px">
                        <div>
                            <div class="fw-semibold small">{{ $conversation->listing->title }}</div>
                            <div class="fw-bold small" style="color:var(--lasu-green)">
                                {{ $conversation->listing->formatted_price }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Meetup Proposals --}}
            <div class="card p-3 mb-3">
                <h6 class="fw-bold mb-3"><i class="bi bi-geo-alt me-1"></i>Meetup Proposals</h6>

                @forelse($conversation->meetupProposals->sortByDesc('created_at') as $proposal)
                    <div class="border rounded p-2 mb-2 small">
                        <div class="fw-semibold">{{ $proposal->campusZone->name }}</div>
                        <div class="text-muted">{{ $proposal->proposed_at->format('d M, h:i A') }}</div>
                        @if($proposal->notes)
                            <div class="text-muted fst-italic">{{ $proposal->notes }}</div>
                        @endif
                        <div class="mt-1">
                            <span class="badge bg-{{ match($proposal->status) {
                                'accepted' => 'success',
                                'declined','cancelled' => 'danger',
                                'counter_proposed' => 'warning text-dark',
                                default => 'secondary'
                            } }}">{{ ucfirst(str_replace('_', ' ', $proposal->status)) }}</span>
                            <span class="text-muted ms-1">by {{ $proposal->proposedBy->name }}</span>
                        </div>
                        @if($proposal->isPending() && $proposal->proposed_by !== auth()->id())
                            <div class="d-flex gap-1 mt-2">
                                <form method="POST" action="{{ route('meetup.accept', $proposal->id) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-success btn-sm">Accept</button>
                                </form>
                                <form method="POST" action="{{ route('meetup.decline', $proposal->id) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-outline-danger btn-sm">Decline</button>
                                </form>
                                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse"
                                        data-bs-target="#counterForm{{ $proposal->id }}">Counter</button>
                            </div>
                            <div class="collapse mt-2" id="counterForm{{ $proposal->id }}">
                                <form method="POST" action="{{ route('meetup.counter', $proposal->id) }}">
                                    @csrf
                                    <select name="campus_zone_id" class="form-select form-select-sm mb-1" required>
                                        <option value="">Select zone...</option>
                                        @foreach(\App\Models\CampusZone::where('is_active',true)->get() as $z)
                                            <option value="{{ $z->id }}">{{ $z->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="datetime-local" name="proposed_at" class="form-control form-control-sm mb-1" required>
                                    <input type="text" name="notes" class="form-control form-control-sm mb-1" placeholder="Notes (optional)">
                                    <button class="btn btn-sm btn-lasu w-100">Send Counter</button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-muted small">No meetup proposals yet.</p>
                @endforelse

                {{-- New proposal form --}}
                <button class="btn btn-outline-secondary btn-sm w-100" data-bs-toggle="collapse" data-bs-target="#newMeetupForm">
                    <i class="bi bi-plus-circle me-1"></i>Propose Meetup
                </button>
                <div class="collapse mt-2" id="newMeetupForm">
                    <form method="POST" action="{{ route('meetup.store', $conversation->id) }}">
                        @csrf
                        <select name="campus_zone_id" class="form-select form-select-sm mb-2" required>
                            <option value="">Select campus zone...</option>
                            @foreach(\App\Models\CampusZone::where('is_active',true)->get() as $z)
                                <option value="{{ $z->id }}">{{ $z->name }}</option>
                            @endforeach
                        </select>
                        <input type="datetime-local" name="proposed_at" class="form-control form-control-sm mb-2" required>
                        <input type="text" name="notes" class="form-control form-control-sm mb-2" placeholder="Notes (optional)">
                        <button class="btn btn-lasu btn-sm w-100">Send Proposal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-scroll to bottom of chat
    const box = document.getElementById('messagesBox');
    if (box) box.scrollTop = box.scrollHeight;
</script>
@endpush
@endsection
