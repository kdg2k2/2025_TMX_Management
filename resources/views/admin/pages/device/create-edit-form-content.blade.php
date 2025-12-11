@php
    $setRequired = true;
    $colClass = 'col-lg-6';
    $emptyOption = false;
@endphp
@include('admin.pages.device.filter-content')
<div class="col-lg-6">
    <div class="my-1">
        <label>Tên thiết bị</label>
        <input type="text" name="name" class="form-control" required>
    </div>
</div>
<div class="col-lg-6">
    <div class="my-1">
        <label>Seri thiết bị
            <small class="text-info">
                (xem trên thiết bị nếu có)
            </small>
        </label>
        <input type="text" name="seri" class="form-control">
    </div>
</div>
