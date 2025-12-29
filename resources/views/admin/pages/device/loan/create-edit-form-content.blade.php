@if (count($devices) > 0)
    <div class="col-md-6">
        <div class="my-1">
            <label>Người mượn</label>
            <select name="created_by" required>
                <x-select-options :items="$users" selected="{{ auth()->id() }}" />
            </select>
        </div>
    </div>

    <div class="col-md-6">
        <div class="my-1">
            <label>Ngày mượn</label>
            <input type="date" class="form-control" name="borrowed_date" value="{{ date('Y-m-d') }}" required>
        </div>
    </div>

    <div class="card clone-container mt-3">
        <div class="card-header">
            Danh sách thiết bị
        </div>
        <div class="card-body clone-row row m-0" id="clone-row">
            <div class="col-lg-3 col-md-6">
                <div class="my-1">
                    <label>Thiết bị</label>
                    <select name="details[0][device_id]" required>
                        <x-select-options :items="$devices" :valueFields="['device_type.name', 'code', 'name']" />
                    </select>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="my-1">
                    <label>Ngày dự kiến trả</label>
                    <input type="date" class="form-control" name="details[0][expected_return_at]"
                        value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="my-1">
                    <label>Vị trí sử dụng
                        <small class="text-info">
                            (ghi rõ phòng ban hoặc mang đi đâu)
                        </small>
                    </label>
                    <input type="text" class="form-control" name="details[0][use_location]" required>
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
        Không còn thiết bị nào sẵn sàng để đăng ký mượn!
    </h3>
</div>
@endif
