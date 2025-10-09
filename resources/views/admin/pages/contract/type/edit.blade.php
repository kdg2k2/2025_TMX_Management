@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Hợp đồng', 'url' => null],
        ['label' => 'Loại hợp đồng', 'url' => route('contract.type.index')],
        ['label' => 'Cập nhật', 'url' => null],
    ]">
        <button class="btn btn-sm btn-success" onclick="window.location='{{ route('contract.type.index') }}'" type="button"
            data-bs-placement="top" data-bs-original-title="Danh sách">
            <i class="ti ti-list"></i>
        </button>
    </x-breadcrumb>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body">
                    <form id="submit-form" class="row"
                        action="{{ route('api.contract.type.update', ['id' => $data['id']]) }}">
                        @method('patch')
                        <div class="my-1 col-md-6">
                            <div class="form-group">
                                <label>
                                    Tên loại
                                </label>
                                <input class="form-control" type="text" name="name" required>
                            </div>
                        </div>
                        <div class="my-1 col-md-6">
                            <div class="form-group">
                                <label>
                                    Mô tả
                                </label>
                                <input class="form-control" type="text" name="description">
                            </div>
                        </div>
                        <div class="my-1 col-12 text-center">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="ti ti-bolt"></i>
                                Thực hiện
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const $data = @json($data ?? null);
    </script>
    <script src="assets/js/http-request/base-store-and-update.js"></script>
@endsection
