<p>
    Người đăng ký:
    <b style="color: blue">
        {{ $data['created_by']['name'] ?? '' }}
    </b>
</p>
<p>
    Thời gian mượn:
    <b>{{ $data['borrowed_date'] ?? '' }}</b>
</p>
<p>
    Thời gian dự kiến trả:
    <b>{{ $data['expected_return_at'] ?? '' }}</b>
</p>
<p>
    Thiết bị mượn:
    <b>{{ implode(
        ' - ',
        collect([$data['device']['device_type']['name'] ?? '', $data['device']['code'] ?? '', $data['device']['name'] ?? ''])->filter()->toArray(),
    ) }}</b>
</p>
<p>
    Sử dụng tại:
    <b>{{ $data['use_location'] ?? '' }}</b>
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

@if ($data['returned_at'])
    <hr>
    <p>
        Thời gian trả:
        <span style="color: green">
            {{ $data['returned_at'] ?? '' }}
        </span>
    </p>
    <p>
        Trạng thái thiết bị khi trả:
        <span style="color: green">
            {{ $data['device_status_return']['converted'] ?? '' }}
        </span>
    </p>
@endif
