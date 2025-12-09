@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Tiến trình gửi mail tự động', 'url' => route('task-schedule.index')],
        ['label' => 'Cập nhật', 'url' => null],
    ]">
        <x-button variant="primary" size="sm" icon="ti ti-list" tooltip="Danh sách" :href="route('task-schedule.index')" />
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <form id="submit-form" class="row"
                action="{{ route('api.task-schedule.update', [
                    'id' => $data['id'],
                ]) }}">
                @method('patch')
                <div class="my-1 col-md-4">
                    <div class="form-group">
                        <label>
                            Tên tiến trình
                        </label>
                        <input class="form-control" type="text" name="name" required>
                    </div>
                </div>
                <div class="my-1 col-md-4">
                    <div class="form-group">
                        <label>
                            Mô tả
                        </label>
                        <input class="form-control" type="text" name="description">
                    </div>
                </div>
                <div class="my-1 col-md-4">
                    <div class="form-group">
                        <label>
                            Tiêu đề email
                        </label>
                        <input class="form-control" type="text" name="subject">
                    </div>
                </div>
                <div class="my-1 col-md-12">
                    <div class="form-group">
                        <label>
                            Nội dung email
                        </label>
                        <textarea name="content" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="my-1 col-md-4">
                    <div class="form-group">
                        <label>
                            Kiểu tiến trình
                        </label>
                        <select name="frequency" required>
                            <x-select-options :items="$frequency" :emptyOption="false" keyField="original"
                                valueFields="converted" />
                        </select>
                    </div>
                </div>
                <div class="my-1 col-md-4">
                    <div class="form-group">
                        <label>
                            Biểu thức cho thời gian chạy tiến trình
                        </label>
                        <input class="form-control" type="text" name="cron_expression" required>
                    </div>
                </div>
                <div class="my-1 col-md-4">
                    <div class="form-group">
                        <label>
                            Lần chạy tiếp theo
                        </label>
                        <input class="form-control" type="datetime-local" name="next_run_at">
                    </div>
                </div>
                <div class="my-1 col-md-4">
                    <div class="form-group">
                        <label>
                            Trạng thái hoạt động
                        </label>
                        <select name="is_active" required>
                            <option value="true">Bật</option>
                            <option value="false">Tắt</option>
                        </select>
                    </div>
                </div>
                <div class="my-1 col-md-4">
                    <div class="form-group">
                        <label>
                            Danh sách người nhận email
                        </label>
                        <select name="users[]" multiple>
                            <x-select-options :items="$users" :emptyOption="false" />
                        </select>
                    </div>
                </div>

                <div class="my-1 col-12 text-center">
                    <x-button-submit />
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const $data = @json($data ?? null);

        inputValueFormatter = {
            next_run_at: (value) => {
                if (!value) return '';
                return value.slice(0, 16);
            },
        };

        selectValueMapping = {
            'users[]': (user) => user?.id || user?.user_id,
            'is_active': (value) => String(value),
        };
    </script>
    <script src="assets/js/http-request/base-store-and-update.js?v={{ time() }}"></script>
@endsection
