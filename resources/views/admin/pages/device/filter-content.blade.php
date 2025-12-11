@php
    $setRequired ??= false;
    $colClass ??= 'col-lg-2 col-md-4';
    $emptyOption ??= true;
@endphp

<div class="{{ $colClass }}">
    <div class="my-1">
        <label>Loại thiết bị</label>
        <select name="device_type_id" id="device-type-id" {{ $setRequired ? 'required' : '' }}>
            <x-select-options :items="$deviceTypes" />
        </select>
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="my-1">
        <label>Trạng thái</label>
        <select name="current_status" id="current-status" {{ $setRequired ? 'required' : '' }}>
            <x-select-options :items="$status" keyField="original" valueFields="converted" :emptyOption="$emptyOption" />
        </select>
    </div>
</div>
