@props([
    'href' => null,
    'variant' => 'success',
    'size' => 'sm',
    'text' => null,
    'icon' => 'ti ti-plus',
    'tooltip' => 'Thêm mới',
    'onclick' => null,
])

<x-button :variant="$variant" :size="$size" :icon="$icon" :tooltip="$tooltip" :text="$text"
    :href="$href" :onclick="$onclick" {{ $attributes }} />
