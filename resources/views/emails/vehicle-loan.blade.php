<p>
    Người đăng ký:
    <b style="color: blue">
        {{ $data['created_by']['name'] ?? '' }}
    </b>
</p>
<p>
    Thời gian lấy xe:
    <b>{{ $data['vehicle_pickup_time'] ?? '' }}</b>
</p>
<p>
    Thời gian dự kiến trả:
    <b style="color: red">{{ $data['estimated_vehicle_return_date'] ?? '' }}</b>
</p>
<p>
    Phương tiện mượn:
    <b>{{ implode(
        ' - ',
        collect([$data['vehicle']['brand'] ?? '', $data['vehicle']['license_plate'] ?? ''])->filter()->toArray(),
    ) }}</b>
</p>
<p>
    Số km hiện trạng:
    <b>{{ number_format($data['current_km'] ?? 0, 0, ',', '.') }}</b>
</p>
<p>
    Điểm đến:
    <b>{{ $data['destination'] ?? '' }}</b>
</p>
<p>
    Nội dung công việc:
    <b>{{ $data['work_content'] ?? '' }}</b>
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
            {{ $data['vehicle_status_return']['converted'] ?? '' }}
        </span>
    </p>
    <p>
        Số km khi trả:
        <b>{{ number_format($data['return_km'] ?? 0, 0, ',', '.') }}</b>
    </p>
    <p>
        Ghi chú:
        <b>{{ $data['note'] ?? '' }}</b>
    </p>
    <p>
        Chi phí xăng xe:
        <b>{{ number_format($data['fuel_cost'] ?? 0, 0, ',', '.') }}</b>vnđ
        {{ $data['fuel_cost_paid_by']['name'] ?? '' }}
    </p>
    <p>
        Chi phí bảo dưỡng:
        <b>{{ number_format($data['maintenance_cost'] ?? 0, 0, ',', '.') }}</b>vnđ
        {{ $data['maintenance_cost_paid_by']['name'] ?? '' }}
    </p>
    <em style="color: green">Ảnh hiện trạng khi trả đính kèm</em>
@else
    <em style="color: green">Ảnh hiện trạng khi mượn đính kèm</em>
@endif
