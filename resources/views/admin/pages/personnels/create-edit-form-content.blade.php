<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Tên nhân sự
        </label>
        <input class="form-control" type="text" name="name" required>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Đơn vị
        </label>
        <select name="personnel_unit_id" required>
            <x-select-options :items="$personnelUnits" :valueFields="['short_name', 'name']" />
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Trình độ học vấn
        </label>
        <input class="form-control" type="text" name="educational_level" required>
    </div>
</div>
@foreach ($fields as $field)
    <div class="my-1 col-md-4">
        <div class="form-group">
            <label>
                {{ $field['name'] }}
            </label>
            <input class="form-control" type="{{ $field['type']['original'] }}" name="{{ $field['field'] }}">
        </div>
    </div>
@endforeach
