@props([
    'id',
    'title' => 'Xác nhận',
    'size' => 'md',
    'action' => null,
    'method' => 'POST',
    'backdrop' => 'static',
    'keyboard' => 'true',
    'centered' => true,
    'scrollable' => false,
    'nested' => false, // Prop để đánh dấu modal lồng nhau
])

@php
    $dialogClass = 'modal-dialog';
    if ($centered) {
        $dialogClass .= ' modal-dialog-centered';
    }
    if ($scrollable) {
        $dialogClass .= ' modal-dialog-scrollable';
    }

    $sizeClass = match ($size) {
        'sm' => ' modal-sm',
        'lg' => ' modal-lg',
        'xl' => ' modal-xl',
        'fullscreen' => ' modal-fullscreen',
        default => '',
    };

    $dialogClass .= $sizeClass;

    // Nếu là nested modal, thêm class để tự tạo backdrop
    $modalClass = 'modal fade';
    if ($nested) {
        $modalClass .= ' bg-dark bg-opacity-50';
    }
@endphp

<div class="{{ $modalClass }}" id="{{ $id }}" data-bs-backdrop="{{ $nested ? 'static' : $backdrop }}"
    data-nested="{{ $nested ? 'true' : 'false' }}" data-bs-keyboard="{{ $keyboard }}" tabindex="-1"
    aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="{{ $dialogClass }}">
        @if ($method)
            <form @if (isset($action)) action="{{ $action }}" @endif class="w-100"
                enctype="multipart/form-data">
                @if (isset($method))
                    @method(strtolower($method))
                @endif

                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="{{ $id }}Label">{{ $title }}</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        {{ $body ?? $slot }}
                    </div>

                    @if (isset($footer))
                        <div class="modal-footer">
                            {{ $footer }}
                        </div>
                    @endif
                </div>
            </form>
        @else
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="{{ $id }}Label">{{ $title }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    {{ $body ?? $slot }}
                </div>

                @if (isset($footer))
                    <div class="modal-footer">
                        {{ $footer }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
