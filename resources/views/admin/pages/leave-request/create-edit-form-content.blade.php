<div class="my-1 col-md-4">
    <label>
        Thời gian bắt đầu
    </label>
    <input class="form-control" type="date" name="from_date" required>
</div>
<div class="my-1 col-md-4">
    <label>
        Thời gian kết thúc
    </label>
    <input class="form-control" type="date" name="to_date" required>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Kiểu đăng ký
        </label>
        <select name="type" required>
            <x-select-options :items="$types" keyField="original" valueFields="converted" :emptyOption="false" />
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <label>
        Tổng số ngày nghỉ
    </label>
    <input class="form-control bg-light" type="text" name="total_leave_days" readonly required>
</div>
<div class="my-1 col-md-4">
    <label>
        Lý do
    </label>
    <input class="form-control" type="text" name="reason" required>
</div>
