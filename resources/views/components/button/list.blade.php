@props([
    'href' => '#',
    'variant' => 'primary',
    'size' => 'sm',
    'text' => null,
    'icon' => 'ti ti-list',
    'tooltip' => 'Danh sách',
])

<x-button :variant="$variant" :size="$size" :icon="$icon" :tooltip="$tooltip" :text="$text" :href="$href"
    {{ $attributes }} />
