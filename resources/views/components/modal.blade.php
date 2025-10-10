@props([
    'id', // bắt buộc có ID modal
    'title' => 'Xác nhận', // tiêu đề mặc định
    'size' => 'md', // sm, md, lg, xl, fullscreen
    'action' => null, // form action
    'method' => 'POST', // form method
    'backdrop' => 'static', // có thể đổi thành true/false
    'keyboard' => 'true', // disable ESC key
    'centered' => true, // căn giữa modal
    'scrollable' => false, // có scroll không
])

@php
    $dialogClass = 'modal-dialog';
    if ($centered) $dialogClass .= ' modal-dialog-centered';
    if ($scrollable) $dialogClass .= ' modal-dialog-scrollable';

    $sizeClass = match ($size) {
        'sm' => ' modal-sm',
        'lg' => ' modal-lg',
        'xl' => ' modal-xl',
        'fullscreen' => ' modal-fullscreen',
        default => '',
    };

    $dialogClass .= $sizeClass;
@endphp

<div class="modal fade" id="{{ $id }}"
     data-bs-backdrop="{{ $backdrop }}"
     data-bs-keyboard="{{ $keyboard }}"
     tabindex="-1"
     aria-labelledby="{{ $id }}Label"
     aria-hidden="true">
    <div class="{{ $dialogClass }}">
        <form class="modal-content" @if($action) action="{{ $action }}" method="{{ strtolower($method) === 'get' ? 'GET' : 'POST' }}" @endif>
            @csrf
            @if(!in_array(strtolower($method), ['get', 'post']))
                @method($method)
            @endif

            {{-- Header --}}
            <div class="modal-header">
                <h6 class="modal-title" id="{{ $id }}Label">{{ $title }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body">
                {{ $body ?? $slot }}
            </div>

            {{-- Footer --}}
            @if (isset($footer))
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @endif
        </form>
    </div>
</div>
