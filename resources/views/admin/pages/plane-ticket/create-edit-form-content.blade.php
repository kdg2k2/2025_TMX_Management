<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Kiểu đăng ký
        </label>
        <select name="type" required>
            <x-select-options :items="$types" :emptyOption="false" keyField="original" valueFields="converted" />
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Hợp đồng
        </label>
        <select name="contract_id" required>
            <x-select-options :items="$contracts" />
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Tên chương trình khác
        </label>
        <input type="text" class="form-control" name="other_program_name" required>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Thời gian bay dự kiến
        </label>
        <input type="datetime-local" class="form-control" name="estimated_flight_time" required>
    </div>
</div>
<div class="my-1 col-md-3">
    <div class="form-group">
        <label>
            Sân bay
        </label>
        <select name="airport_id" required>
            <x-select-options :items="$airports" />
        </select>
    </div>
</div>
<div class="my-1 col-md-3">
    <div class="form-group">
        <label>
            Hãng bay
        </label>
        <select name="airline_id" required>
            <x-select-options :items="$airlines" />
        </select>
    </div>
</div>
<div class="my-1 col-md-3">
    <div class="form-group">
        <label>
            Hạng vé
        </label>
        <select name="plane_ticket_class_id" required>
            <x-select-options :items="$planeTicketClasses" />
        </select>
    </div>
</div>
<div class="my-1 col-md-3">
    <div class="form-group">
        <label>
            Số cân hành lý ký gửi (kg)
        </label>
        <input type="number" class="form-control" name="checked_baggage_allowances" required min="0"
            value="0">
    </div>
</div>

<div class="card custom-card mt-3">
    <div class="card-header">
        Danh sách thành viên
    </div>
    <div class="card-body row clone-container">
        <div class="clone-row row" id="clone-row">
            <div class="my-1 col-md-4">
                <label>
                    Kiểu thành viên
                </label>
                <select name="details[0][user_type]" class="user-type" required>
                    <x-select-options :items="$userTypes" :emptyOption="false" keyField="original" valueFields="converted" />
                </select>
            </div>
            <div class="my-1 col-md-7">
                <label>
                    Thành viên nội bộ
                </label>
                <select name="details[0][user_id]">
                    <x-select-options :items="$users" />
                </select>
            </div>
            <div class="my-1 col-md-7" hidden>
                <label>
                    Tên thành viên ngoài
                </label>
                <input type="text" class="form-control" name="details[0][external_user_name]">
            </div>
            <div class="col-1 d-flex justify-content-center align-items-end">
                <div class="my-1">
                    <x-button variant="success" icon="ti ti-plus" tooltip="Thêm dòng"></x-button>
                </div>
            </div>
        </div>
    </div>
</div>
