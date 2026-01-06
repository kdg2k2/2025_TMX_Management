@props([
    'text' => null,
    'color' => 'secondary',
    'icon' => null,
    'pill' => true,
    'textColor' => null,
    'class' => null,
])

@php
    $classes = collect([
        'badge',
        $pill ? 'rounded-pill' : null,
        $textColor ? "text-{$textColor}" : null,
        "bg-{$color}",
        $class,
    ])
        ->filter()
        ->implode(' ');
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if ($icon)
        <i class="{{ $icon }} {{ $text ? 'me-1' : '' }}"></i>
    @endif

    {{ $text ?? $slot }}
</span>
