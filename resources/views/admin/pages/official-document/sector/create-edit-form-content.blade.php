<div class="col-md-4 col-12 my-1">
    <div class="form-group">
        <label for="name">Tên loại lĩnh vực</label>
        <input type="text" name="name" id="name" class="form-control" required>
    </div>
</div>
<div class="col-md-4 col-12 my-1">
    <div class="form-group">
        <label for="description">Mô tả loại lĩnh vực</label>
        <input type="text" name="description" id="description" class="form-control">
    </div>
</div>
<div class="col-md-4 col-12 my-1">
    <div class="form-group">
        <label for="users">Người nhận email</label>
        <select name="users[]" id="users" class="form-control" required multiple>
            <x-select-options :items="$users" :emptyOption="false"/>
        </select>
    </div>
</div>
