@if (count($vehicles) > 0)
    <div class="col-lg-4 col-md-6">
        <div class="my-1">
            <label>Phương tiện</label>
            <select name="vehicle_id" id="vehicle-id" required>
                <x-select-options :items="$vehicles" :valueFields="['brand', 'license_plate']" recordAttribute="data-record"/>
            </select>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="my-1">
            <label>
                Số km hiện trạng
            </label>
            <input type="number" class="form-control" name="current_km" required>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="my-1">
            <label>Thời gian lấy xe</label>
            <input type="datetime-local" class="form-control" name="vehicle_pickup_time" value="{{ date('Y-m-d H:i') }}"
                required>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="my-1">
            <label>Ngày dự kiến trả</label>
            <input type="date" class="form-control" name="estimated_vehicle_return_date" value="{{ date('Y-m-d') }}"
                required>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="my-1">
            <label>
                Điểm đến
            </label>
            <input type="text" class="form-control" name="destination" required>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="my-1">
            <label>
                Nội dung công việc
            </label>
            <input type="text" class="form-control" name="work_content" required>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="my-1">
            <label>
                Ảnh hiện trạng xe phía trước
            </label>
            <input type="file" class="form-control" name="before_front_image" accept=".png,.jpg,.jpeg,.webp"
                required>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="my-1">
            <label>
                Ảnh hiện trạng xe phía sau
            </label>
            <input type="file" class="form-control" name="before_rear_image" accept=".png,.jpg,.jpeg,.webp"
                required>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="my-1">
            <label>
                Ảnh hiện trạng xe phía trái
            </label>
            <input type="file" class="form-control" name="before_left_image" accept=".png,.jpg,.jpeg,.webp"
                required>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="my-1">
            <label>
                Ảnh hiện trạng xe phía phải
            </label>
            <input type="file" class="form-control" name="before_right_image" accept=".png,.jpg,.jpeg,.webp"
                required>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="my-1">
            <label>
                Ảnh hiện trạng công tơ mét
            </label>
            <input type="file" class="form-control" name="before_odometer_image" accept=".png,.jpg,.jpeg,.webp"
                required>
        </div>
    </div>
@else
    <div class="d-flex justify-content-center">
        <h5>
            Không còn phương tiện nào sẵn sàng để đăng ký mượn!
        </h5>
    </div>
@endif
