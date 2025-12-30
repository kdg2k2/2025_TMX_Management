<p>
    Người đăng ký:
    <b style="color: blue">
        {{ $data['created_by']['name'] ?? '' }}
    </b>
</p>
<p>
    Thiết bị sửa:
    <b>{{ implode(
        ' - ',
        collect([
            $data['device']['device_type']['name'] ?? '',
            $data['device']['code'] ?? '',
            $data['device']['name'] ?? '',
        ])->filter()->toArray(),
    ) }}</b>
</p>
<p>
    Nội dung kiến nghị:
    <b>{{ $data['suggested_content'] ?? '' }}</b>
</p>
<p>
    Hiện trạng thiết bị:
    <b>{{ $data['device_status'] ?? '' }}</b>
</p>
<p>
    Ghi chú:
    <b>{{ $data['note'] ?? '' }}</b>
</p>

@if ($data['approved_at'])
    <hr>
    <p>
        Thời gian phê duyệt:
        <span style="color: red">
            {{ $data['approved_at'] ?? '' }}
        </span>
    </p>
    <p>
        Người phê duyệt:
        <span style="color: red">
            {{ $data['approved_by']['name'] ?? '' }}
        </span>
    </p>
    <p>
        Nhận xét phê duyệt:
        <span style="color: red">
            {{ $data['approval_note'] ?? ($data['rejection_note'] ?? '') }}
        </span>
    </p>
@endif

@if ($data['fixed_at'])
    <hr>
    <p>
        Thời gian sửa xong:
        <span style="color: green">
            {{ $data['fixed_at'] ?? '' }}
        </span>
    </p>
    <p>
        Kinh phí sửa chữa (vnđ):
        <span style="color: green">
            {{ number_format($data['repair_costs'] ?? '', 0, ',', '.') }}
        </span>
    </p>
@endif
