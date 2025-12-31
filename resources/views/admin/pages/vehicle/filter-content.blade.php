@php
    $setRequired ??= false;
    $colClass ??= 'col-lg-2 col-md-4';
@endphp
<div class="{{ $colClass }}">
    <div class="my-1">
        <label>Trạng thái</label>
        <select name="status" id="status" {{ $setRequired ? 'required' : '' }}>
            <x-select-options :items="$status" keyField="original" valueFields="converted" />
        </select>
    </div>
</div>
