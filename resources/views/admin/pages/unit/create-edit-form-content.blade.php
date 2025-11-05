<div class="col-md-6 col-12">
    <div class="form-group">
        <label for="name">Tên đơn vị</label>
        <input type="text" name="name" id="name" class="form-control" required>
    </div>
</div>
<div class="col-md-6 col-12">
    <div class="form-group">
        <label for="province_code">Tỉnh</label>
        <select name="province_code" id="province_code" class="form-control" required>
            <option value="">Chọn tỉnh</option>
            <x-select-options :items="$provinces" keyField="code" valueFields="name" />
        </select>
    </div>
</div>
