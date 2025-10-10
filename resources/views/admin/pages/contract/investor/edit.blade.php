@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Hợp đồng', 'url' => null],
        ['label' => 'Nhà đầu tư', 'url' => route('contract.investor.index')],
        ['label' => 'Cập nhật', 'url' => null],
    ]">
        <button class="btn btn-sm btn-success" onclick="window.location='{{ route('contract.investor.index') }}'"
            type="button" data-bs-placement="top" data-bs-original-title="Danh sách">
            <i class="ti ti-list"></i>
        </button>
    </x-breadcrumb>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body">
                    <form id="submit-form" class="row"
                        action="{{ route('api.contract.investor.update', ['id' => $data['id']]) }}">
                        @method('patch')
                        @include('admin.pages.contract.investor.create-edit-form-content')
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
