@php
    $colClass = 'col-lg-4 col-md-6';
    $setRequired = true;
    $emptyOpt ??= false;
@endphp
@include('admin/pages/official-document/incoming/filters')
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Hợp đồng
        </label>
        <select name="contract_id" class="form-control" required>
            <x-select-options :items="$contracts" />
        </select>
    </div>
</div>
<div class="{{ $colClass }}" hidden>
    <div class="form-group">
        <label>
            Tên chương trình khác
        </label>
        <input type="text" name="other_program_name" class="form-control">
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Số, ký hiệu văn bản
        </label>
        <input type="text" name="document_number" class="form-control" required>
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Ngày phát hành
        </label>
        <input type="date" name="issuing_date" class="form-control">
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Ngày đến
        </label>
        <input type="date" name="received_date" class="form-control" required>
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Trích yêu nội dung
        </label>
        <input type="text" name="content_summary" class="form-control" required>
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Nơi gửi
        </label>
        <input type="text" name="sender_address" class="form-control" required>
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Họ tên người ký
        </label>
        <input type="text" name="signer_name" class="form-control" required>
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Chức danh người ký
        </label>
        <input type="text" name="signer_position" class="form-control" required>
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Họ tên người liên hệ
        </label>
        <input type="text" name="contact_person_name" class="form-control">
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Địa chỉ người liên hệ
        </label>
        <input type="text" name="contact_person_address" class="form-control">
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Số điện thoại người liên hệ
        </label>
        <input type="text" name="contact_person_phone" class="form-control">
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Ghi chú
        </label>
        <input type="text" name="notes" class="form-control">
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            File đính kèm
        </label>
        <input type="file" name="attachment_file" class="form-control" required accept=".pdf">
    </div>
</div>
