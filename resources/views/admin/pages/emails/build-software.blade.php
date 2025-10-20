@switch($data['type'])
    @case('accept')
    @break

    <h5></h5>
    @case('reject')
    @break

    @case('update_state')
    @break

    @default
    @break
@endswitch

Tên phần mềm: <b>{{ $data['data']['name'] }}</b>
Thuộc hợp đồng: <b>{{ $data['data']['contract']['name'] ?? 'N/A' }}</b>
Trường hợp xây dựng: <b>{{ $data['data']['development_case']['converted'] }}</b>
Mô tả phần mềm: <b>{{ $data['data']['description'] ?? 'N/A' }}</b>

<i>File mô tả phần mềm được gửi đính kèm</i>
