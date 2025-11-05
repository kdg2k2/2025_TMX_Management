<div class="my-1 col-md-6">
    <div class="form-group">
        <label>
            Ngày họp
        </label>
        <input type="date" class="form-control" name="meeting_day" required>
    </div>
</div>
<div class="my-1 col-md-6">
    <div class="form-group">
        <label>
            Nội dung chính
        </label>
        <input type="text" class="form-control" name="main_content" required>
    </div>
</div>
<div class="my-1 col-md-6">
    <div class="form-group">
        <label>
            Tuần
        </label>
        <select name="week" required>
            <x-select-options :items="$weeks" keyField="week_number" valueFields="label"></x-select-options>
        </select>
    </div>
</div>
<div class="my-1 col-md-6">
    <div class="form-group">
        <label id="input-file-label">
            Chọn file (pdf)
        </label>
        <input class="form-control" type="file" name="path" accept=".pdf" required>
    </div>
</div>
