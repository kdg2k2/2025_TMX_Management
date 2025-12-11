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
    <div class="form-group">
        <label>
            Sân bay khởi hành
        </label>
        <select name="departure_airport_id" required>
            <x-select-options :items="$airports" />
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Sân bay đến
        </label>
        <select name="return_airport_id" required>
            <x-select-options :items="$airports" />
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Hãng bay
        </label>
        <select name="airline_id" required>
            <x-select-options :items="$airlines" />
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Hạng vé
        </label>
        <select name="plane_ticket_class_id" required>
            <x-select-options :items="$planeTicketClasses" />
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Số cân hành lý ký gửi (kg)
        </label>
        <input type="number" class="form-control" name="checked_baggage_allowances" required min="0" value="0">
    </div>
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
