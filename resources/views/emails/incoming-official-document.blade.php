<p>
    Loại văn bản:
    <strong>
        {{ $data['official_document_type']['name'] ?? '' }}
    </strong>
</p>
<p>
    Thuộc chương trình:
    <strong style="color: green">
        @if ($data['program_type'] == 'contract')
            {{ $data['contract']['name'] ?? '' }}
        @else
            {{ $data['other_program_name'] ?? '' }}
        @endif
    </strong>
</p>
<p>
    Số, ký hiệu văn bản:
    <strong style="color: green">
        {{ $data['document_number'] ?? '' }}
    </strong>
</p>
<p>
    Ngày phát hành:
    <strong>
        {{ $data['issuing_date'] ?? '' }}
    </strong>
</p>
<p>
    Ngày đến:
    <strong>
        {{ $data['received_date'] ?? '' }}
    </strong>
</p>
<p>
    Trích yêu nội dung:
    <strong>
        {{ $data['content_summary'] ?? '' }}
    </strong>
</p>
<p>
    Nơi gửi:
    <strong>
        {{ $data['sender_address'] ?? '' }}
    </strong>
</p>
<p>
    Họ tên người ký:
    <strong>
        {{ $data['signer_name'] ?? '' }}
    </strong>
</p>
<p>
    Chức danh người ký:
    <strong>
        {{ $data['signer_position'] ?? '' }}
    </strong>
</p>
<p>
    Họ tên người liên hệ:
    <strong>
        {{ $data['contact_person_name'] ?? '' }}
    </strong>
</p>
<p>
    Địa chỉ người liên hệ:
    <strong>
        {{ $data['contact_person_address'] ?? '' }}
    </strong>
</p>
<p>
    Số điện thoại người liên hệ:
    <strong>
        {{ $data['contact_person_phone'] ?? '' }}
    </strong>
</p>
<p>
    Ghi chú:
    <strong>
        {{ $data['notes'] ?? '' }}
    </strong>
</p>
<hr>
<p>
    Trạng thái nhiệm vụ:
    <strong style="color: blue">
        {{ $data['status']['converted'] }}
    </strong>
</p>
<p>
    Người thực hiện nhiệm vụ:
    <strong style="color: red">
        {{ $data['task_assignee']['name'] ?? '' }}
    </strong>
</p>
<p>
    Thành viên hỗ trợ nhiệm vụ:
    <strong style="color: red">
        {{ implode(', ', array_map(fn($i) => $i['user']['name'], $data['incoming_official_document_users'])) }}
    </strong>
</p>
<p>
    Hạn hoàn thành nhiệm vụ:
    <strong style="color: red">
        {{ $data['task_completion_deadline'] ?? '' }}
    </strong>
</p>
<p>
    Ghi chú nhiệm vụ:
    <strong>
        {{ $data['task_notes'] ?? '' }}
    </strong>
</p>
<p>
    Thời gian giao nhiệm vụ:
    <strong>
        {{ $data['assign_at'] ?? '' }}
    </strong>
</p>
@if (!empty($data['complete_at']))
    <p>
        Thời gian hoàn thành nhiệm vụ:
        <strong>
            {{ $data['complete_at'] ?? '' }}
        </strong>
    </p>
@endif
