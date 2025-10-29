<p>
    Người đăng ký:
    <b style="color: blue">
        {{ $data['created_by']['name'] ?? '' }}
    </b>
</p>
<p>
    Lý do:
    {{ $data['reason'] ?? '' }}
</p>
<p>
    Thời gian:
    <b>
        {{ $data['from_date'] }} - {{ $data['to_date'] }} - Tổng {{ $data['total_leave_days'] }} ngày
    </b>
</p>

@if (isset($data['approval_note']))
    <hr>
    <p>
        Trạng thái phê duyệt: <b style="color: red;">{{ $data['approval_status']['converted'] }}</b>
    </p>
    <p>
        Ghi chú phê duyệt: <b>{{ $data['approval_note'] ?? '' }}</b>
    </p>
    <p>
        Ngày phê duyệt: <b>{{ $data['approval_date'] ?? '' }}</b>
    </p>
    <p>
        Người phê duyệt: <b style="color: red;">{{ $data['approved_by']['name'] ?? '' }}</b>
    </p>
@endif

@if (isset($data['adjust_approval_note']))
    <hr>
    <p>
        Trạng thái phê duyệt điểu chỉnh nghỉ phép: <b
            style="color: red;">{{ $data['adjust_approval_status']['converted'] }}</b>
    </p>
    <p>
        Ghi chú phê duyệt điểu chỉnh nghỉ phép: <b>{{ $data['adjust_approval_note'] ?? '' }}</b>
    </p>
    <p>
        Ngày phê duyệt điểu chỉnh nghỉ phép: <b>{{ $data['adjust_approval_date'] ?? '' }}</b>
    </p>
    <p>
        Người phê duyệt điểu chỉnh nghỉ phép: <b style="color: red;">{{ $data['adjust_approved_by']['name'] ?? '' }}</b>
    </p>
@endif
