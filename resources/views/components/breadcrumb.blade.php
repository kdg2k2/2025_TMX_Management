@props(['items' => []])

@if (count($items) > 0)
    <div class="page-header-breadcrumb mb-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="flex-grow-1">
                <h1 class="page-title fw-medium fs-18 mb-1">
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
                    </ol>
                @endif
            </div>

            @if (trim($slot))
                <div class="d-flex align-items-center mt-2 mt-md-0 ms-md-3">
                    {{ $slot }}
                </div>
            @endif
        </div>
    </div>
@endif
