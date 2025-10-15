<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Tên
        </label>
        <input class="form-control" type="text" name="name" required>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Email
        </label>
        <input class="form-control" type="email" name="email" required>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Mật khẩu
        </label>
        <input class="form-control" type="password" name="password">
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Số điện thoại
        </label>
        <input class="form-control" type="text" name="phone">
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Số căn cước
        </label>
        <input class="form-control" type="text" name="citizen_identification_number">
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Ảnh đại diện (png,jpg,jpeg,webp)
        </label>
        <input class="form-control" type="file" name="path" accept=".png,.jpg,.jpeg,.webp">
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Ảnh chữ ký (png,jpg,jpeg,webp)
        </label>
        <input class="form-control" type="file" name="path_signature" accept=".png,.jpg,.jpeg,.webp">
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Phòng
        </label>
        <select name="department_id" required>
            <x-select-options :items="$departments"></x-select-options>
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Chức vụ
        </label>
        <select name="position_id" required>
            <x-select-options :items="$positions"></x-select-options>
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Chức danh
        </label>
        <select name="job_title_id" required>
            <x-select-options :items="$jobTitles"></x-select-options>
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Quyền truy cập
        </label>
        <select name="role_id">
            <x-select-options :items="$roles"></x-select-options>
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Khóa tài khoản
        </label>
        <select name="is_banned" required>
            <option value="0">Không</option>
            <option value="1">Có</option>
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Nghỉ việc
        </label>
        <select name="retired" required>
            <option value="0">Không</option>
            <option value="1">Có</option>
        </select>
    </div>
</div>
