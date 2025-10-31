@include('admin.pages.user.base-create-edit-form')
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
        <select name="is_retired" required>
            <option value="0">Không</option>
            <option value="1">Có</option>
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Tính lương
        </label>
        <select name="is_salary_counted" required>
            <option value="0">Không</option>
            <option value="1">Có</option>
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Cơ hữu
        </label>
        <select name="is_permanent" required>
            <option value="0">Không</option>
            <option value="1">Có</option>
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Con nhỏ
        </label>
        <select name="is_childcare_mode" required>
            <option value="0">Không</option>
            <option value="1">Có</option>
        </select>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Mức lương
        </label>
        <input class="form-control" type="number" name="salary_level">
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Mức tiêu chí (số tiền phạt khi vi phạm quy chế)
        </label>
        <input class="form-control" type="number" name="violation_penalty">
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Phụ cấp liên lạc
        </label>
        <input class="form-control" type="number" name="allowance_contact">
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Phụ cấp chức vụ
        </label>
        <input class="form-control" type="number" name="allowance_position">
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Phụ cấp xăng xe
        </label>
        <input class="form-control" type="number" name="allowance_fuel">
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Phụ cấp đi lại
        </label>
        <input class="form-control" type="number" name="allowance_transport">
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Ngày bắt đầu đi làm (Để tính công, tính lương)
        </label>
        <input class="form-control" type="date" name="work_start_date">
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>
            Ngày kết thúc đi làm (Để tính công, tính lương)
        </label>
        <input class="form-control" type="date" name="work_end_date">
    </div>
</div>
