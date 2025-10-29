@php
    $colClass = $colClass ?? 'col-md-4'; // Default cho trang create
@endphp

<div class="my-1 {{ $colClass }}">
    <div class="form-group">
        <label>
            Kiểu đăng ký
        </label>
        <select name="type" id="leave-type" required>
            <x-select-options :items="$types" keyField="original" valueFields="converted" :emptyOption="false" />
        </select>
    </div>
</div>
<div class="my-1 {{ $colClass }}">
    <label>
        Thời gian bắt đầu
    </label>
    <input class="form-control" type="date" name="from_date" id="from-date" required>
</div>
<div class="my-1 {{ $colClass }}" id="to-date-wrapper">
    <label>
        Thời gian kết thúc
    </label>
    <input class="form-control" type="date" name="to_date" id="to-date" required>
</div>
<div class="my-1 {{ $colClass }}">
    <label>
        Tổng số ngày nghỉ
    </label>
    <input class="form-control bg-light" type="text" name="total_leave_days" id="total-leave-days" readonly required>
</div>
