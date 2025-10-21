@if (isset($rejectionReason))
    <p>
        Lý do từ chối: <b style="color: red;">{{ $rejectionReason }}</b>
    </p>
@endif

<p>
    Tên phần mềm: <b>{{ $data['name'] }}</b>
</p>
<p>
    Thuộc hợp đồng: <b>{{ $data['contract']['name'] ?? 'N/A' }}</b>
</p>
<p>
    Trường hợp xây dựng: <b>{{ $data['development_case']['converted'] }}</b>
</p>
<p>
    Mô tả phần mềm: <b>{{ $data['description'] ?? 'N/A' }}</b>
</p>
<p>
    Người đặc tả xây dựng phần mềm:
<ul>
    @foreach ($data['business_analysts'] as $item)
        <li>{{ $item['user']['name'] ?? 'N/A' }}</li>
    @endforeach
</ul>
</p>
<p>
    Thành viên thực hiện xây dựng phầm mềm
<ul>
    @foreach ($data['members'] as $item)
        <li>{{ $item['user']['name'] ?? 'N/A' }}</li>
    @endforeach
</ul>
</p>
<p>
    Tình trạng thực hiện: <b style="color: blue">{{ $data['state']['converted'] ?? 'N/A' }}</b>
</p>
