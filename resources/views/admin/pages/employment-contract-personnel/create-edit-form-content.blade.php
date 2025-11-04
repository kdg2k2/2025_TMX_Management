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
            Số căn cước công dân
        </label>
        <input class="form-control" type="text" name="citizen_identification_number" required>
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
