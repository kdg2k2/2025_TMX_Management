<p>
    Người đăng ký:
    <b style="color: blue">
        {{ $data['created_by']['name'] ?? '' }}
    </b>
</p>
<p>
    Địa chỉ:
    {{ $data['address'] ?? '' }}
</p>
<p>
    Thời gian:
    <b>
        {{ $data['from_date'] }} - {{ $data['to_date'] }} - Tổng {{ $data['total_trip_days'] }} ngày
    </b>
</p>
<p>
    Nội dung công tác:
    <b style="color: blue">
        {{ $data['content'] ?? '' }}
    </b>
</p>
<p>
    Thuộc chương trình: {{ $data['type_program']['converted'] ?? '' }} -
    <i style="color: green">
        {{ $data['type_program']['original'] == 'contract' ? $data['contract']['name'] : $data['other_program'] }}
    </i>
</p>
<p>
    Đầu mối:
    {{ $data['clue'] ?? '' }}
</p>
<p>
    Thành phần:
    {{ $data['participants'] ?? '' }}
</p>
<p>
    Ghi chú:
    {{ $data['note'] ?? '' }}
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

@if (isset($data['return_datetime']))
    <hr>
    <p>
        Thời gian về: <b style="color: blue;">{{ $data['return_datetime'] }}</b>
    </p>
@endif

@if (isset($data['return_approval_note']))
    <p>
        Trạng thái phê duyệt kết thúc công tác: <b style="color: red;">{{ $data['return_approval_status']['converted'] }}</b>
    </p>
    <p>
        Ghi chú phê duyệt kết thúc công tác: <b>{{ $data['return_approval_note'] ?? '' }}</b>
    </p>
    <p>
        Ngày phê duyệt kết thúc công tác: <b>{{ $data['return_approval_date'] ?? '' }}</b>
    </p>
    <p>
        Người phê duyệt kết thúc công tác: <b style="color: red;">{{ $data['return_approved_by']['name'] ?? '' }}</b>
    </p>
@endif

@if (isset($data['total_work_days']))
    <hr>
    <p>
        Tổng số ngày công công tác: <b style="color: blue;">{{ $data['total_work_days'] }}</b>
    </p>
@endif
