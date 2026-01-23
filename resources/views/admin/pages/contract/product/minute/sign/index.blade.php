@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Hợp đồng', 'url' => null],
        ['label' => 'Sản phẩm', 'url' => route('contract.product.index')],
        ['label' => 'Ký biên bản', 'url' => null],
    ]">
    </x-breadcrumb>

    <div class="row">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="ti ti-signature me-2"></i>
                        Ký biên bản sản phẩm hợp đồng
                    </h5>
                </div>
                <div class="card-body">
                    <div id="signature-container" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                        <p class="mt-3 text-muted">Đang tải dữ liệu biên bản...</p>
                    </div>

                    <div id="minute-info" class="d-none">
                        <!-- Thông tin biên bản sẽ được load bằng JavaScript -->
                    </div>

                    <div id="signature-form" class="d-none mt-4">
                        <h6 class="mb-3">
                            <i class="ti ti-pencil me-2"></i>
                            Thực hiện ký
                        </h6>
                        <form id="submit-signature-form"
                            action="{{ route('contract.product.minute.sign', ['minute_id' => request()->query('minute_id')]) }}">
                            @method('patch')
                            <input type="hidden" name="minute_id" id="minute-id">

                            <div class="mb-3">
                                <label class="form-label">Chọn phương thức ký</label>
                                <select name="signature_type" id="signature-type" class="form-control" required>
                                    <option value="">-- Chọn phương thức --</option>
                                    <option value="profile">Dùng chữ ký cá nhân</option>
                                    <option value="draw">Ký tay (vẽ chữ ký)</option>
                                    <option value="upload">Tải lên ảnh chữ ký</option>
                                </select>
                            </div>

                            <div id="draw-signature-container" class="mb-3 d-none">
                                <label class="form-label">Vẽ chữ ký của bạn</label>

                                <div class="d-flex justify-content-center">
                                    <div class="border rounded p-2 bg-light">
                                        <canvas id="signature-canvas"
                                            style="width: 500px; height: 120px; cursor: crosshair;">
                                        </canvas>
                                    </div>
                                </div>

                                <div class="mt-2 text-center">
                                    <button type="button" class="btn btn-sm btn-secondary" id="clear-signature">
                                        <i class="ti ti-eraser"></i> Xóa và vẽ lại
                                    </button>
                                </div>
                            </div>

                            <div id="upload-signature-container" class="mb-3 d-none">
                                <label class="form-label">Chọn file ảnh chữ ký (PNG, JPG)</label>
                                <input type="file" name="signature_file" id="signature-file" class="form-control"
                                    accept="image/png,image/jpeg">
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('contract.product.index') }}" class="btn btn-light">
                                    <i class="ti ti-arrow-left"></i> Quay lại
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-signature"></i> Xác nhận ký
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const minuteId = @json(request()->query('minute_id'));
        const apiContractProductMinuteShow = @json(route('api.contract.product.minute.show'));
        const apiMinuteDetail = @json(route('api.contract.product.minute.list'));
        const contractProductIndex = @json(route('contract.product.index'));
        const authId = @json(auth()->id());
    </script>
    <script src="assets/js/contract/product/sign.js?v={{ time() }}"></script>
@endsection
