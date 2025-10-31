<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Địa điểm
        </label>
        <input class="form-control" type="text" name="address" required>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Ngày bắt đầu
        </label>
        <input class="form-control" type="date" name="from_date" required>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Ngày kết thúc
        </label>
        <input class="form-control" type="date" name="to_date" required>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Kiểu chương trình
        </label>
        <select name="type_program" required>
            <x-select-options :items="$typeProgram" keyField="original" valueFields="converted" :emptyOption="false" />
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Hợp đồng
        </label>
        <select name="contract_id" required>
            <x-select-options :items="$contracts" keyField="id" valueFields="name" />
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Tên chương trình khác
        </label>
        <input class="form-control" type="text" name="other_program" required>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Nội dung công tác
        </label>
        <input class="form-control" type="text" name="content" required>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Đầu mối
        </label>
        <input class="form-control" type="text" name="clue">
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Thành phần tham gia
        </label>
        <input class="form-control" type="text" name="participants">
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Ghi chú
        </label>
        <input class="form-control" type="text" name="note">
    </div>
</div>
