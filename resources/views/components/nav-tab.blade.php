@props([
    'id' => 'navTab' . uniqid(),
    'style' => 'pills',
    'justified' => false,
    'vertical' => false,
    'tabs' => [],
])

@php
    $navClass = 'nav border-bottom';

    $navClass .= match ($style) {
        'tabs' => ' nav-tabs',
        'underline' => ' nav-underline',
        default => ' nav-pills nav-style-2',
    };

    if ($justified === 'fill') {
        $navClass .= ' nav-fill';
    } elseif ($justified === true || $justified === 'justified') {
        $navClass .= ' nav-justified';
    }

    if ($vertical) {
        $navClass .= ' flex-column';
    }
@endphp

<div {{ $attributes->merge(['class' => $vertical ? 'row' : '']) }}>
    <div class="{{ $vertical ? 'col-md-3 col-xl-2' : '' }}">
        <ul class="{{ $navClass }}" role="tablist">
            @foreach ($tabs as $index => $tab)
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $index === 0 ? 'active' : '' }}"
                        data-bs-toggle="tab"
                        role="tab"
                        href="#{{ $id }}-tab-{{ $index }}"
                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                        {!! $index > 0 ? 'tabindex="-1"' : '' !!}
                        @if (isset($tab['onclick'])) onclick="{{ $tab['onclick'] }}" @endif>
                        @if (isset($tab['icon']))
                            <i class="{{ $tab['icon'] }} me-1"></i>
                        @endif
                        {{ $tab['title'] }}
                        @if (isset($tab['badge']))
                            <span class="badge bg-{{ $tab['badge']['color'] ?? 'primary' }} ms-2"
                                @if (isset($tab['badge']['id'])) id="{{ $tab['badge']['id'] }}" @endif>
                                {{ $tab['badge']['text'] }}
                            </span>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="{{ $vertical ? 'col-md-9 col-xl-10' : '' }}">
        <div class="tab-content">
            @foreach ($tabs as $index => $tab)
                <div class="tab-pane {{ $index === 0 ? 'active show' : '' }} {{ $tab['class'] ?? '' }}"
                    id="{{ $id }}-tab-{{ $index }}"
                    role="tabpanel">
                    {!! $tab['content'] !!}
                </div>
            @endforeach
        </div>
    </div>
</div>
