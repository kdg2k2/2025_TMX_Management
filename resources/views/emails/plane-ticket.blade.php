<p>
    Người đăng ký:
    <b style="color: blue">
        {{ $data['created_by']['name'] ?? '' }}
    </b>
</p>
<p>
    Kiểu đăng ký:
    {{ $data['type']['converted'] ?? '' }}
</p>
<p>
    Tên chương trình:
    <b style="color: green">
        {{ $data['contract']['name'] ?? $data['other_program_name'] }}
    </b>
</p>
<p>
    Thời gian dự kiến bay:
    <b>{{ $data['estimated_flight_time'] ?? '' }}</b>
</p>
<p>
    Sân bay:
    <b>{{ $data['airport']['name'] ?? '' }}</b>
</p>
<p>
    Hãng bay:
    <b>{{ $data['airline']['name'] ?? '' }}</b>
</p>
<p>
    Hạng vé:
    <b>{{ $data['plane_ticket_class']['name'] ?? '' }}</b>
</p>
<p>
    Số cân hành lý ký gửi (kg):
    <b>{{ $data['checked_baggage_allowances'] ?? '' }}</b>
</p>
<p>
    Thành viên công tác:
<ul>
    @foreach ($data['details'] as $item)
        <li>
            <b style="color: blue">{{ $item['user_type']['converted'] }}</b> -
            <b>{{ $item['user']['name'] ?? $item['external_user_name'] }}</b>
        </li>
    @endforeach
</ul>
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
