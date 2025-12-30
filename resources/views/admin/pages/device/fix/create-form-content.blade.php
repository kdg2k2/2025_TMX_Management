@if (count($devices) > 0)
    <div class="clone-container">
        <div class="clone-row row m-0" id="clone-row">
            <div class="col-lg-3 col-md-6">
                <div class="my-1">
                    <label>Thiết bị</label>
                    <select name="details[0][device_id]" required>
                        <x-select-options :items="$devices" :valueFields="['device_type.name', 'code', 'name']" />
                    </select>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="my-1">
                    <label>
                        Nội dung kiến nghị
                    </label>
                    <input type="text" class="form-control" name="details[0][suggested_content]" required>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="my-1">
                    <label>
                        Hiện trạng thiết bị
                    </label>
                    <input type="text" class="form-control" name="details[0][device_status]" required>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="my-1">
                    <label>Ghi chú</label>
                    <input type="text" class="form-control" name="details[0][note]">
                </div>
            </div>
            <div class="col-1 d-flex justify-content-center align-items-end">
                <div class="my-1">
                    <x-button variant="success" icon="ti ti-plus" tooltip="Thêm dòng"></x-button>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="d-flex justify-content-center">
        <h5>
            Không còn thiết bị nào để đăng ký sửa chữa!
        </h5>
    </div>
@endif
