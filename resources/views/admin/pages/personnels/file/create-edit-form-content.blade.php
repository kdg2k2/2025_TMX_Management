<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Nhân sự
        </label>
        <select name="personnel_id" required>
            <x-select-options :items="$personnels"></x-select-options>
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Loại file
        </label>
        <select name="type_id" required>
            <x-select-options :items="$personnelFileTypes" recordAttribute="data-record"></x-select-options>
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label id="input-file-label">
            Chọn file
        </label>
        <input class="form-control" type="file" name="path">
    </div>
</div>
