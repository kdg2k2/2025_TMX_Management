<p>
    Tên văn bản:
    <strong style="color: green">
        {{ $data['name'] ?? '' }}
    </strong>
</p>
@if (isset($data['official_document_sector']))
    <p>
        Loại lĩnh vực:
        <strong>
            {{ $data['official_document_sector']['name'] ?? '' }}
        </strong>
    </p>
@endif
@if (isset($mailData['old_reviewer']))
    <p>
        Người kiểm tra (cũ):
        <strong>
            {{ $mailData['old_reviewer'] ?? '' }}
        </strong>
    </p>
@endif
@if (isset($data['reviewed_by']))
    <p>
        Người kiểm tra{{ isset($mailData['old_reviewer']) ? ' (mới):' : ':' }}
        <strong>
            {{ $data['reviewed_by']['name'] ?? '' }}
        </strong>
    </p>
@endif
<p>
    Kiểu chương trình:
    <strong style="color: green">
        {{ $data['program_type']['converted'] ?? '' }}
    </strong>
</p>
<p>
    Thuộc chương trình:
    <strong style="color: green">
        @switch($data['program_type']['original'])
            @case('contract')
                {{ $data['contract']['name'] ?? '' }}
            @break

            @case('incoming')
                {{ $data['incoming_official_document']['name'] ?? '' }}
            @break

            @default
                {{ $data['other_program_name'] ?? '' }}
        @endswitch
    </strong>
</p>
<p>
    Kiểu phát hành:
    <strong style="color: green">
        {{ $data['release_type']['converted'] ?? '' }}
    </strong>
</p>
<p>
    Loại văn bản:
    <strong>
        {{ $data['official_document_type']['name'] ?? '' }}
    </strong>
</p>
<p>
    Ngày dự kiến phát hành:
    <strong>
        {{ $data['expected_release_date'] ?? '' }}
    </strong>
</p>
<p>
    Người ký:
    <strong>
        {{ $data['signed_by']['name'] ?? '' }}
    </strong>
</p>
<p>
    Nơi nhận:
    <strong>
        {{ $data['receiver_organization'] ?? '' }}
    </strong>
</p>
<p>
    Họ tên người nhận trực tiếp:
    <strong>
        {{ $data['receiver_name'] ?? '' }}
    </strong>
</p>
<p>
    Địa chỉ nơi nhận:
    <strong>
        {{ $data['receiver_address'] ?? '' }}
    </strong>
</p>
<p>
    Điện thoại liên hệ nơi nhận:
    <strong>
        {{ $data['receiver_phone'] ?? '' }}
    </strong>
</p>
<p>
    Ghi chú:
    <strong>
        {{ $data['note'] ?? '' }}
    </strong>
</p>
