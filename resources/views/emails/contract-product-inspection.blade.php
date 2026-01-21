<p>
    Thông tin hợp đồng:
<ul>
    <li>
        Năm:
        <b>
            {{ $data['contract']['year'] ?? '' }}
        </b>
    </li>
    <li>
        Số hợp đồng:
        <b>
            {{ $data['contract']['contract_number'] ?? '' }}
        </b>
    </li>
    <li>
        Tên hợp đồng:
        <b style="color: blue">
            {{ $data['contract']['name'] ?? '' }}
        </b>
    </li>
    <li>
        Chủ đầu tư:
        <b>
            {{ implode(
                ', ',
                collect([$data['contract']['investor']['name_vi'], $data['contract']['investor']['name_en']])->filter()->toArray(),
            ) }}
        </b>
    </li>
    <li>
        PT chuyên môn:
        <b>
            {{ implode(
                ', ',
                collect($data['contract']['professionals'])->map(fn($i) => $i['user']['name'] ?? null)->filter()->toArray(),
            ) }}
        </b>
    </li>
    <li>
        Link GG Drive:
        @if ($data['contract']['ggdrive_link'])
            <a href="{{ $data['contract']['ggdrive_link'] }}" target="_blank">
                {{ $data['contract']['ggdrive_link'] }}
            </a>
        @endif
    </li>
    <li>
        Nhân sự thực hiện:
        <ul>
            <li>
                <b>
                    Người hoàn thiện:
                </b>
                {{ $data['contract']['executor_user']['name'] ?? '' }}
            </li>
            <li>
                <b>
                    Người hoàn thiện:
                </b>
                {{ $data['contract']['inspector_user']['name'] ?? '' }}
            </li>
            <li>
                <b>
                    Người phối hợp làm SPTG:
                </b>
                {{ implode(
                    ', ',
                    collect($data['contract']['intermediate_collaborators'])->map(fn($i) => $i['user']['name'] ?? null)->filter()->toArray(),
                ) }}
            </li>
        </ul>
    </li>
</ul>
</p>
<hr>
<p>
    Người yêu cầu kiểm tra:
    <b style="color: green">
        {{ $data['created_by']['name'] ?? '' }}
    </b>
</p>
<p>
    Người kiểm tra:
    <b style="color: green">
        {{ $data['inspector_user']['name'] ?? '' }}
    </b>
</p>
<p>
    Người hỗ trợ kiểm tra:
    <b style="color: green">
        {{ $data['supported_by']['name'] ?? '' }}
    </b>
</p>
<p>
    Mô tả cần hỗ trợ:
    <b style="color: green">
        {{ $data['support_description'] ?? '' }}
    </b>
</p>
<p>
    Ghi chú:
    <b style="color: green">
        {{ $data['support_description'] ?? '' }}
    </b>
</p>
@if (isset($data['inspector_comment']))
    <hr>
    <p>
        Nhận xét của người kiểm tra:
        <b style="color: red">
            {{ $data['inspector_comment'] ?? '' }}
        </b>
    </p>
@endif
