@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, success, danger, warning, info, light, dark
    'size' => 'sm', // sm, md, lg
    'icon' => null,
    'iconPosition' => 'left', // left, right
    'text' => null,
    'href' => null,
    'onclick' => null,
    'tooltip' => null,
    'outline' => false,
    'disabled' => false,
    'loading' => false,
])

@php
    // Build class
    $btnClass = 'btn';

    // Size
    $btnClass .= match($size) {
        'sm' => ' btn-sm',
        'lg' => ' btn-lg',
        default => '',
    };

    // Variant
    $btnClass .= $outline
        ? " btn-outline-{$variant}"
        : " btn-{$variant}";

    // Disabled
    if ($disabled || $loading) {
        $btnClass .= ' disabled';
    }

    // Build attributes
    $attrs = $attributes->merge([
        'class' => $btnClass,
        'type' => $type,
    ]);

    // Add tooltip attributes
    if ($tooltip) {
        $attrs = $attrs->merge([
            'data-bs-toggle' => 'tooltip',
            'data-bs-placement' => 'top',
            'title' => $tooltip,
            'aria-label' => $tooltip,
        ]);
    }

    // Add onclick
    if ($onclick) {
        $attrs = $attrs->merge(['onclick' => $onclick]);
    }

    // Add disabled
    if ($disabled || $loading) {
        $attrs = $attrs->merge(['disabled' => true]);
    }

    // Determine if href is provided (link button)
    $isLink = !empty($href);
@endphp

@if($isLink)
    <a href="{{ $href }}" {{ $attrs->except(['type']) }}>
        @if($loading)
            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
        @elseif($icon && $iconPosition === 'left')
            <i class="{{ $icon }}"></i>
        @endif

        @if($text || $slot->isNotEmpty())
            <span class="{{ $icon ? 'ms-1' : '' }}">{{ $text ?? $slot }}</span>
        @endif

        @if($icon && $iconPosition === 'right' && !$loading)
            <i class="{{ $icon }} ms-1"></i>
        @endif
    </a>
@else
    <button {{ $attrs }}>
        @if($loading)
            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
        @elseif($icon && $iconPosition === 'left')
            <i class="{{ $icon }}"></i>
        @endif

        @if($text || $slot->isNotEmpty())
            <span class="{{ $icon ? 'ms-1' : '' }}">{{ $text ?? $slot }}</span>
        @endif

        @if($icon && $iconPosition === 'right' && !$loading)
            <i class="{{ $icon }} ms-1"></i>
        @endif
    </button>
@endif
