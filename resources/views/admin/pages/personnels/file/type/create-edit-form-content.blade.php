<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Tên loại
        </label>
        <input class="form-control" type="text" name="name" required>
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
            Các định dạng cho phép
        </label>
        <select name="extensions[]" multiple required>
            <x-select-options :items="$extensions" valueFields="extension" :emptyOption="false"></x-select-options>
        </select>
    </div>
</div>
