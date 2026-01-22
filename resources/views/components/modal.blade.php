@props([
    'id',
    'title' => 'Xác nhận',
    'size' => 'md',
    'fullscreen' => false,
    'action' => null,
    'method' => null,
    'backdrop' => 'static',
    'keyboard' => 'true',
    'centered' => true,
    'scrollable' => false,
    'nested' => false,
])


@php
    $dialogClass = 'modal-dialog';
    if ($centered) {
        $dialogClass .= ' modal-dialog-centered';
    }
    if ($scrollable) {
        $dialogClass .= ' modal-dialog-scrollable';
    }

    if ($fullscreen) {
        $dialogClass .= ' modal-fullscreen pe-3';
    } else {
        $dialogClass .= match ($size) {
            'sm' => ' modal-sm',
            'lg' => ' modal-lg',
            'xl' => ' modal-xl',
            default => '',
        };
    }

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

                    @if ((isset($body) && trim($body) !== '') || (isset($slot) && trim((string) $slot) !== ''))
                        <div class="modal-body">
                            {{ $body ?? $slot }}
                        </div>
                    @endif


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

                @if ((isset($body) && trim($body) !== '') || (isset($slot) && trim((string) $slot) !== ''))
                    <div class="modal-body">
                        {{ $body ?? $slot }}
                    </div>
                @endif


                @if (isset($footer))
                    <div class="modal-footer">
                        {{ $footer }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
