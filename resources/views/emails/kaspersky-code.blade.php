@php
    $deviceInfo = implode(
        ' - ',
        collect([
            $data['device']['device_type']['name'] ?? null,
            $data['device']['code'] ?? null,
            $data['device']['name'] ?? null,
            $data['device']['seri'] ?? null,
        ])
            ->filter()
            ->toArray(),
    );
@endphp

<p>
    Họ tên:
    <b style="color:red">
        {{ $data['created_by']['name'] }}
    </b>
</p>
<p>
    Lý do đăng ký:
    <b>
        {{ $data['reason'] }}
    </b>
</p>

<p>
    Thiết bị đăng ký:
    <b>
        {{ $data['type']['converted'] }}
    </b>
    @if (in_array($data['type']['original'], ['company', 'both']) && $deviceInfo)
        <i style="color: blue">
            ({{ $deviceInfo }})
        </i>
    @endif
</p>

@if ($data['approved_at'])
    <hr>
    <p>
        Thời gian phê duyệt:
        <span style="color:red">
            {{ $data['approved_at'] }}
        </span>
    </p>
    <p>
        Người phê duyệt:
        <span style="color:red">
            {{ $data['approved_by']['name'] ?? '' }}
        </span>
    </p>
    <p>
        Nhận xét phê duyệt:
        <span style="color:red">
            {{ $data['approval_note'] ?? ($data['rejection_note'] ?? '') }}
        </span>
    </p>
@endif

@if (isset($assignedCodes))
    <hr>
    <p>
        Mã kaspersky:
    <ul>
        @foreach ($assignedCodes as $item)
            <li>
                <b>{{ $item }}</b>
            </li>
        @endforeach
    </ul>
    </p>
@endif
