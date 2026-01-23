<p>
    Người tạo:
    <b style="color: green">{{ $minute['created_by']['name'] ?? '' }}</b>
</p>
<p>
    <b style="color: #0d6efd;">Thông tin hợp đồng:</b>
</p>
<ul>
    <li>
        Năm:
        <b>{{ $minute['contract']['year'] ?? '' }}</b>
    </li>
    <li>
        Số hợp đồng:
        <b>{{ $minute['contract']['contract_number'] ?? '' }}</b>
    </li>
    <li>
        Tên hợp đồng:
        <b style="color: blue">{{ $minute['contract']['name'] ?? '' }}</b>
    </li>
    <li>
        Chủ đầu tư:
        <b>
            {{ implode(', ',collect([$minute['contract']['investor']['name_vi'] ?? null, $minute['contract']['investor']['name_en'] ?? null])->filter()->toArray()) }}
        </b>
    </li>
    <li>
        Người hướng dẫn:
        <b>
            {{ implode(', ',collect($inspection['contract']['instructors'] ?? [])->map(fn($i) => $i['user']['name'] ?? null)->filter()->toArray()) }}
        </b>
    </li>
    <li>
        Phụ trách chuyên môn:
        <b>
            {{ $minute['contract_professional']['user']['name'] ?? '' }}
        </b>
    </li>
    <li>
        Phụ trách giải ngân:
        <b>
            {{ $minute['contract_disbursement']['user']['name'] ?? '' }}
        </b>
    </li>
    <li>
        Phụ trách hoàn thiện sản phẩm:
        <b>
            {{ $inspection['contract']['executor_user']['name'] ?? '' }}
        </b>
    </li>
    <li>
        Phụ trách kiểm tra:
        <b>
            {{ $inspection['inspector_user']['name'] ?? '' }}
        </b>
    </li>
</ul>
