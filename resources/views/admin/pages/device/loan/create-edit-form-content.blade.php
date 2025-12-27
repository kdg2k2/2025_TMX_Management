<div class="clone-container">
    <div class="card">
        <div class="card-body row clone-row" id="clone-row">
            <div class="col-lg-3 col-md-6">
                <div class="my-1">
                    <label>Thiết bị</label>
                    <select name="device_id" required>
                        <x-select-options :items="$devices" />
                    </select>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="my-1">
                    <label>Ngày mượn</label>
                    <input type="date" class="form-control" name="borrowed_date" required>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="my-1">
                    <label>Ngày dự kiến trả</label>
                    <input type="date" class="form-control" name="expected_return_at" required>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="my-1">
                    <label>Ghi chú</label>
                    <input type="text" class="form-control" name="note">
                </div>
            </div>
            <div class="col-1 d-flex justify-content-center align-items-end">
                <div class="my-1">
                    <x-button variant="success" icon="ti ti-plus" tooltip="Thêm dòng"></x-button>
                </div>
            </div>
        </div>
    </div>
</div>
