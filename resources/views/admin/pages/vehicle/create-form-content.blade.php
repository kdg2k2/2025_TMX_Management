@php
    $setRequired = true;
    $colClass = 'col-md-4';
@endphp
@include('admin.pages.vehicle.filter-content')
<div class="my-1 col-md-4">
    <div class="form-group">
        <label for="brand">
            Hãng xe
        </label>
        <input type="text" name="brand" id="brand" class="form-control" required>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label for="license_plate">
            Biển số xe
        </label>
        <input type="text" name="license_plate" id="license-plate" class="form-control" required>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label for="current_km">
            Số km hiện trạng
        </label>
        <input type="number" name="current_km" id="current-km" class="form-control" required>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label for="maintenance_km">
            Số km đến hạn bảo dưỡng
        </label>
        <input type="number" name="maintenance_km" id="maintenance-km" class="form-control">
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label for="inspection_expired_at">
            Hạn đăng kiểm
        </label>
        <input type="date" name="inspection_expired_at" id="inspection-expired-at" class="form-control">
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label for="liability_insurance_expired_at">
            Hạn bảo hiểm trách nhiệm dân sự
        </label>
        <input type="date" name="liability_insurance_expired_at" id="liability-insurance-expired-at"
            class="form-control">
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label for="body_insurance_expired_at">
            Hạn bảo hiểm thân vỏ
        </label>
        <input type="date" name="body_insurance_expired_at" id="body-insurance-expired-at" class="form-control">
    </div>
</div>
