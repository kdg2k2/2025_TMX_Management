<div class="my-1 col-md-4">
    <label>
        Kiểu thành viên
    </label>
    <select name="user_type" required disabled>
        <x-select-options :items="$userTypes" :emptyOption="false" keyField="original" valueFields="converted" />
    </select>
</div>
@if (isset($data['user_id']))
    <div class="my-1 col-md-4">
        <label>
            Thành viên nội bộ
        </label>
        <select required disabled>
            <x-select-options :items="[$data['user']->toArray()]" :emptyOption="false" keyField="id" valueFields="name" />
        </select>
    </div>
@else
    <div class="my-1 col-md-4">
        <label>
            Tên thành viên ngoài
        </label>
        <input type="text" class="form-control" name="external_user_name" disabled required>
    </div>
@endif
<div class="my-1 col-md-4">
    <label>
        Ngày khởi hành
    </label>
    <input type="date" class="form-control" name="departure_date" required>
</div>
<div class="my-1 col-md-4">
    <label>
        Ngày về
    </label>
    <input type="date" class="form-control" name="return_date">
</div>
<div class="my-1 col-md-4">
    <label>
        Nơi khởi hành
    </label>
    <input type="text" class="form-control" name="departure_place" required>
</div>
<div class="my-1 col-md-4">
    <label>
        Nơi về
    </label>
    <input type="text" class="form-control" name="return_place">
</div>
<div class="my-1 col-md-4">
    <label>
        Số hiệu tàu xe
    </label>
    <input type="text" class="form-control" name="train_number">
</div>
<div class="my-1 col-md-4">
    <label>
        Giá vé (vnđ)
    </label>
    <input type="text" class="form-control" name="ticket_price" id="ticket_price" required>
</div>
<div class="my-1 col-md-4">
    <label>
        Ảnh vé (png,jpg,jpeg,pdf)
    </label>
    <input type="file" class="form-control" name="ticket_image_path" accept=".png,.jpg,.jpeg,.pdf">
</div>
<div class="my-1 col-md-4">
    <label>
        Ghi chú
    </label>
    <input type="text" class="form-control" name="note">
</div>
