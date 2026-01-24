<div class="my-1 col-md-6">
    <div class="form-group">
        <label>
            Tên
        </label>
        <input class="form-control" type="text" name="name" required>
    </div>
</div>
<div class="my-1 col-md-6">
    <div class="form-group">
        <label>
            Email
        </label>
        <input class="form-control" type="email" name="email" required>
    </div>
</div>
<div class="my-1 col-md-6">
    <div class="form-group">
        <label>
            Mật khẩu mới <small class="text-muted">(Để trống nếu không đổi)</small>
        </label>
        <input class="form-control" type="password" name="password" autocomplete="new-password">
    </div>
</div>
<div class="my-1 col-md-6">
    <div class="form-group">
        <label>
            Số điện thoại
        </label>
        <input class="form-control" type="text" name="phone">
    </div>
</div>
<div class="my-1 col-md-6">
    <div class="form-group">
        <label>
            Số căn cước
        </label>
        <input class="form-control" type="text" name="citizen_identification_number">
    </div>
</div>
<div class="my-1 col-md-6">
    <div class="form-group">
        <label>
            Ngày sinh
        </label>
        <input class="form-control" type="date" name="date_of_birth">
    </div>
</div>
<div class="my-1 col-md-12">
    <div class="form-group">
        <label>
            Địa chỉ
        </label>
        <input class="form-control" type="text" name="address">
    </div>
</div>
<div class="my-1 col-md-6">
    <div class="form-group">
        <label>
            Ảnh đại diện (png, jpg, jpeg, webp)
        </label>
        <input class="form-control" type="file" name="path" accept=".png,.jpg,.jpeg,.webp">
        @if (!empty($user['path']))
            <div class="mt-2">
                <img src="{{ $user['path'] }}" alt="Avatar" class="img-thumbnail" style="max-width: 120px; height: 80px;">
            </div>
        @endif
    </div>
</div>
<div class="my-1 col-md-6">
    <div class="form-group">
        <label>
            Ảnh chữ ký (png, jpg, jpeg, webp)
        </label>
        <input class="form-control" type="file" name="path_signature" accept=".png,.jpg,.jpeg,.webp">
        @if (!empty($user['path_signature']))
            <div class="mt-2">
                <img src="{{ $user['path_signature'] }}" alt="Signature" class="img-thumbnail" style="max-width: 120px; height: 80px;">
            </div>
        @endif
    </div>
</div>

<!-- Thông tin read-only -->
<div class="col-12 mt-4 mb-2">
    <h6 class="text-muted">Thông tin tổ chức (không thể chỉnh sửa)</h6>
    <hr class="my-0">
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>Phòng ban</label>
        <input class="form-control" type="text" value="{{ $user['department']['name'] ?? '' }}" readonly disabled>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>Chức vụ</label>
        <input class="form-control" type="text" value="{{ $user['position']['name'] ?? '' }}" readonly disabled>
    </div>
</div>
<div class="my-1 col-md-4">
    <div class="form-group">
        <label>Chức danh</label>
        <input class="form-control" type="text" value="{{ $user['job_title']['name'] ?? '' }}" readonly disabled>
    </div>
</div>
