@props(['items' => []])

@if (count($items) > 0)
    <div class="page-header-breadcrumb mb-3">
        <div class="d-flex align-center justify-content-between flex-wrap">
            <h1 class="page-title fw-medium fs-18 mb-0">
                {{ end($items)['label'] ?? '' }}
            </h1>
            @if (count($items) > 1)
                <ol class="breadcrumb mb-0">
                    @foreach ($items as $index => $item)
                        @php
                            $isLast = $index === count($items) - 1;
                        @endphp
                        <li class="breadcrumb-item {{ $isLast ? 'active' : '' }}">
                            @if ($isLast)
                                {{ $item['label'] }}
                            @else
                                <a href="{{ $item['url'] ?? 'javascript:void(0);' }}">{{ $item['label'] }}</a>
                            @endif
                        </li>
                    @endforeach
            @endif
            </ol>
        </div>
    </div>
@endif
