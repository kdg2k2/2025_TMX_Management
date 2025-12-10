@props([
    'text' => 'Thực hiện',
    'icon' => 'ti ti-bolt',
    'variant' => 'primary',
    'size' => 'sm',
    'loading' => false,
    'loadingText' => 'Đang xử lý...',
    'form' => null,
])

<x-button type="submit" :variant="$variant" :size="$size" :icon="$loading ? null : $icon" :text="$loading ? $loadingText : $text"
    :loading="$loading" :form="$form" {{ $attributes }}>
    {{ $slot }}
</x-button>
