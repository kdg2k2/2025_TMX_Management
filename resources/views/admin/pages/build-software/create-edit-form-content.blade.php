<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Tên phần mềm
        </label>
        <input class="form-control" type="text" name="name" required>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Thuộc gói thầu
        </label>
        <select name="contract_id">
            <x-select-options :items="$contracts" />
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Trường hợp xây dựng
        </label>
        <select name="development_case" required>
            <x-select-options :items="$developmentCases" keyField="original" valueFields="converted"></x-select-options>
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Mô tả
        </label>
        <input class="form-control" type="text" name="description">
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            File mô tả (docx,pdf,rar,zip)
        </label>
        <input class="form-control" type="file" name="attachment" accept=".docx,.pdf,.rar,.zip">
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Thời hạn
        </label>
        <input class="form-control" type="date" name="deadline" required>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Người đặc tả xây dựng phần mềm
        </label>
        <select name="business_analysts[]" required multiple>
            <x-select-options :items="$users"></x-select-options>
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Thành viên thực hiện xây dựng phầm mềm
        </label>
        <select name="members[]" required multiple>
            <x-select-options :items="$users"></x-select-options>
        </select>
    </div>
</div>
