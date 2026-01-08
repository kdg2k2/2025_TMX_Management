@php
    $colClass = 'col-lg-4 col-md-6 my-1';
    $setRequired = true;
    $emptyOpt ??= false;
@endphp
@include('admin/pages/official-document/incoming/base-inputs')
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Hợp đồng
        </label>
        <select name="contract_id" id="contract-id" class="form-control">
            <x-select-options :items="$contracts" />
        </select>
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Tên chương trình khác
        </label>
        <input type="text" name="other_program_name" id="other-program-name" class="form-control">
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Số, ký hiệu văn bản
        </label>
        <input type="text" name="document_number" id="document-number" class="form-control" required>
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Ngày phát hành
        </label>
        <input type="date" name="issuing_date" id="issuing-date" class="form-control">
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Ngày đến
        </label>
        <input type="date" name="received_date" id="received-date" class="form-control" required>
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Trích yêu nội dung
        </label>
        <input type="text" name="content_summary" id="content-summary" class="form-control" required>
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Nơi gửi
        </label>
        <input type="text" name="sender_address" id="sender-address" class="form-control" required>
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Họ tên người ký
        </label>
        <input type="text" name="signer_name" id="signer-name" class="form-control" required>
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Chức danh người ký
        </label>
        <input type="text" name="signer_position" id="signer-position" class="form-control" required>
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Họ tên người liên hệ
        </label>
        <input type="text" name="contact_person_name" id="contact-person-name" class="form-control">
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Địa chỉ người liên hệ
        </label>
        <input type="text" name="contact_person_address" id="contact-person-address" class="form-control">
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Số điện thoại người liên hệ
        </label>
        <input type="text" name="contact_person_phone" id="contact-person-phone" class="form-control">
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Ghi chú
        </label>
        <input type="text" name="notes" id="notes" class="form-control">
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            File đính kèm (pdf)
        </label>
        <input type="file" name="attachment_file" id="attachment-file" class="form-control"
            {{ isset($data) ? '' : 'required' }} accept=".pdf">
    </div>
</div>
