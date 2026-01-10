<div class="{{ !isset($data) ? 'col-md-3' : 'col-md-4' }} my-1">
    <div class="form-group">
        <label>
            Mã kaspersky
        </label>
        <input type="text" name="code" id="code" class="form-control" required>
    </div>
</div>
@if (!isset($data))
    <div class="{{ !isset($data) ? 'col-md-3' : 'col-md-4' }} my-1">
        <div class="form-group">
            <label>
                Tổng số lượng (máy) cho phép sử dụng
            </label>
            <input type="number" name="total_quantity" id="total_quantity" class="form-control" required>
        </div>
    </div>
@endif
<div class="{{ !isset($data) ? 'col-md-3' : 'col-md-4' }} my-1">
    <div class="form-group">
        <label>
            Thời hạn sử dụng (ngày)
        </label>
        <input type="number" name="valid_days" id="valid_days" class="form-control" required>
    </div>
</div>
<div class="{{ !isset($data) ? 'col-md-3' : 'col-md-4' }} my-1">
    <div class="form-group">
        <label>
            Hình ảnh
        </label>
        <input type="file" name="path" id="path" class="form-control" accept=".jpg,.png,.jpeg">
    </div>
</div>
