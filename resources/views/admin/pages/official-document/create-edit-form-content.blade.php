<div class="col-md-4 my-1">
    <div class="form-group">
        <label>
            Tên văn bản
        </label>
        <input type="text" name="name" class="form-control" required>
    </div>
</div>
@if ($authPosition < 5)
    <div class="col-md-4 my-1">
        <div class="form-group">
            <label>
                Loại lĩnh vực
            </label>
            <select name="official_document_sector_id" class="form-control" required>
                <x-select-options :items="$officialDocumentSectors" />
            </select>
        </div>
    </div>
@else
    <div class="col-md-4 my-1">
        <div class="form-group">
            <label>
                Người kiểm tra
            </label>
            <select name="reviewed_by" class="form-control" required>
                <x-select-options :items="$reviewers" />
            </select>
        </div>
    </div>
@endif
<div class="col-md-4 my-1">
    <div class="form-group">
        <label>
            Người nhận thông tin
        </label>
        <select name="users[]" class="form-control" required multiple>
            <x-select-options :items="$users" :emptyOption="false" />
        </select>
    </div>
</div>
@include('admin.pages.official-document.base-inputs')
<div class="col-md-6 my-1">
    <div class="form-group">
        <label>
            Chọn nhiệm vụ
        </label>
        <select name="incoming_official_document_id" id="incoming-official-document-id" class="form-control">
            <x-select-options :items="$incomingOfficialDocuments" />
        </select>
    </div>
</div>
<div class="col-md-6 my-1">
    <div class="form-group">
        <label>
            Chọn hợp đồng
        </label>
        <select name="contract_id" id="contract-id" class="form-control">
            <x-select-options :items="$contracts" />
        </select>
    </div>
</div>
<div class="col-md-6 my-1">
    <div class="form-group">
        <label>
            Tên chương trình khác
        </label>
        <input type="text" name="other_program_name" id="other-program-name" class="form-control">
    </div>
</div>
<div class="col-md-3 my-1">
    <div class="form-group">
        <label>
            Ngày dự kiến phát hành
        </label>
        <input type="date" name="expected_release_date" class="form-control" required>
    </div>
</div>
<div class="col-md-3 my-1">
    <div class="form-group">
        <label>
            Người ký
        </label>
        <select name="signed_by" class="form-control" required>
            <x-select-options :items="$users" />
        </select>
    </div>
</div>
<div class="col-md-3 my-1">
    <div class="form-group">
        <label>
            Nơi nhận
        </label>
        <input type="text" name="receiver_organization" class="form-control" required>
    </div>
</div>
<div class="col-md-3 my-1">
    <div class="form-group">
        <label>
            Họ tên người nhận trực tiếp
        </label>
        <input type="text" name="receiver_name" class="form-control" required>
    </div>
</div>
<div class="col-md-3 my-1">
    <div class="form-group">
        <label>
            Địa chỉ nơi nhận
        </label>
        <input type="text" name="receiver_address" class="form-control" required>
    </div>
</div>
<div class="col-md-3 my-1">
    <div class="form-group">
        <label>
            Điện thoại liên hệ nơi nhận
        </label>
        <input type="text" name="receiver_phone" class="form-control" required>
    </div>
</div>
<div class="col-md-3 my-1">
    <div class="form-group">
        <label>
            Ghi chú
        </label>
        <input type="text" name="note" class="form-control">
    </div>
</div>
<div class="col-md-3 my-1">
    <div class="form-group">
        <label>
            File soạn thảo (docx)
        </label>
        <input type="file" name="pending_review_docx_file" class="form-control" required accept=".docx">
    </div>
</div>
