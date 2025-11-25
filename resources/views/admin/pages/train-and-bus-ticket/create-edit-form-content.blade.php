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
            Thời gian đi dự kiến
        </label>
        <input type="date" class="form-control" name="estimated_travel_time" required>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Điểm khởi hành dự kiến
        </label>
        <input type="text" class="form-control" name="expected_departure" required>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Điểm đến dự kiến
        </label>
        <input type="text" class="form-control" name="expected_destination" required>
    </div>
</div>

<div class="card custom-card mt-3">
    <div class="card-header">
        Danh sách thành viên
    </div>
    <div class="card-body row clone-container">
        <div class="col-12 row" id="clone-row">
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
