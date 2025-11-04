<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Tên hiển thị
        </label>
        <input class="form-control" type="text" name="name" required>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Định dạng
        </label>
        <select name="type">
            <x-select-options :items="$types" keyField="original" valueFields="converted" />
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Độ ưu tiên -
            <span class="text-info">
                Số càng lớn sẽ càng được đặt xếp lên trước
            </span>
        </label>
        <input class="form-control" type="number" name="z_index" id="z_index">
    </div>
</div>
