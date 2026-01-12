@php
    $colClass ??= 'col-md-4';
    $setRequired ??= true;
    $setEmptyOption ??= false;
@endphp
<div class="my-1 {{ $colClass }}">
    <div class="form-group">
        <label>
            Kiểu đăng ký
        </label>
        <select name="type" id="type" {{ $setRequired ? 'required' : '' }}>
            <x-select-options :items="$types" :emptyOption="$setEmptyOption" keyField="original" valueFields="converted" />
        </select>
    </div>
</div>
<div class="my-1 {{ $colClass }}">
    <div class="form-group">
        <label>
            Thiết bị
        </label>
        <select name="device_id" id="device-id" {{ $setRequired ? 'required' : '' }}>
            <x-select-options :items="$devices" :valueFields="['device.device_type.name', 'device.code', 'device.name']" />
        </select>
    </div>
</div>
