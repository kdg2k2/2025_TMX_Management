<div class="my-1 col-md-6">
    <div class="form-group">
        <label>
            Tên hiển thị
        </label>
        <input class="form-control" type="text" name="name" required>
    </div>
</div>
<div class="my-1 col-md-6">
    <div class="form-group">
        <label>
            Định dạng
        </label>
        <select name="type">
            <x-select-options :items="$types" keyField="original" valueFields="converted"/>
        </select>
    </div>
</div>
