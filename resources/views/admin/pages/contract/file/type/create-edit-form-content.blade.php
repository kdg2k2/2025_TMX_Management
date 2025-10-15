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
<div class="my-1 col-md-1">
    <div class="form-group">
        <label>
            Loại
        </label>
        <select name="type" id="type" required>
            <x-select-options :items="$types" :emptyOption="false" keyField="original"
                valueFields="converted"></x-select-options>
        </select>
    </div>
</div>
<div class="my-1 col-md-3" hidden>
    <div class="form-group">
        <label>
            Các định dạng cho phép
        </label>
        <select name="extensions[]" multiple>
            <x-select-options :items="$extensions" valueFields="extension" :emptyOption="false"></x-select-options>
        </select>
    </div>
</div>
