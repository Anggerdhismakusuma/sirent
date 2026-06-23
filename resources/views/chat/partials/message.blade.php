{{-- SI-RENT Chat Message Bubble Partial --}}
@props(['msg', 'isMine' => false])

@php
$bubbleStyle = $isMine
    ? 'background:#0031e1; border-radius:5px 5px 0 5px; color:#fff;'
    : 'background:#ebebeb; border-radius:0 10px 10px 10px; color:#000;';
$timeColor = $isMine ? 'rgba(255,255,255,0.7)' : '#888';
@endphp

<div class="d-flex {{ $isMine ? 'justify-content-end' : 'justify-content-start' }} mb-3">
    @if(!$isMine)
        <div class="flex-shrink-0 me-2">
            <x-shared.avatar
                :imagePath="$msg['sender_avatar'] ?? null"
                :name="$msg['sender_name'] ?? 'User'"
                size="sm"
            />
        </div>
    @endif
    <div class="d-flex flex-column {{ $isMine ? 'align-items-end' : 'align-items-start' }}" style="max-width:70%;">
        {{-- Sender name (only for other user) --}}
        @if(!$isMine)
            <small class="mb-1 fw-semibold" style="font-family:'Mona Sans',sans-serif; font-size:11px; color:#545353;">
                {{ $msg['sender_name'] ?? 'User' }}
            </small>
        @endif

        {{-- Message bubble --}}
        <div style="{{ $bubbleStyle }} padding:8px 14px; font-family:'Mona Sans',sans-serif; font-size:14px; word-wrap:break-word;">
            @if(!empty($msg['body']))
                <p class="mb-0">{{ $msg['body'] }}</p>
            @endif
            @if(!empty($msg['attachment_url']))
                <img src="{{ $msg['attachment_url'] }}" alt="attachment"
                     class="img-fluid rounded-2 mt-1"
                     style="max-height:200px; cursor:pointer;"
                     onclick="window.open(this.src, '_blank')">
            @endif
        </div>

        {{-- Timestamp + read receipt --}}
        <div class="d-flex align-items-center gap-1 mt-1">
            <span style="font-family:'Mona Sans',sans-serif; font-size:10px; color:{{ $timeColor }};">
                {{ $msg['time_formatted'] ?? '' }}
            </span>
            @if($isMine)
                <i class="bi {{ !empty($msg['is_read']) ? 'bi-check-all' : 'bi-check' }}"
                   style="font-size:12px; color:{{ !empty($msg['is_read']) ? '#4fc3f7' : 'rgba(255,255,255,0.5)' }};"></i>
            @endif
        </div>
    </div>
</div>
