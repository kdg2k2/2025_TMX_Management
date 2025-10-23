@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Xây dựng gói thầu', 'url' => route('bidding.index')],
        ['label' => 'Chi tiết', 'url' => null],
    ]">
        <x-button variant="primary" size="sm" icon="ti ti-list" tooltip="Danh sách" :href="route('bidding.index')" />
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <x-nav-tab id="contract-detail-tab" style="pills" :tabs="[
                [
                    'title' => 'Kinh nghiệm nhà thầu',
                    'icon' => 'ti ti-building',
                    'content' => view('admin.pages.bidding.partials.contractor-experience', [
                        'biddingContractorExperienceFileTypes' => $biddingContractorExperienceFileTypes,
                    ])->render(),
                    'onclick' => '()=>{}',
                ],
                [
                    'title' => 'Tư cách hợp lệ',
                    'icon' => 'ti ti-certificate',
                    'content' => view('admin.pages.bidding.partials.eligibility')->render(),
                    'onclick' => '()=>{}',
                ],
                [
                    'title' => 'Sở hữu phần mềm',
                    'icon' => 'ti ti-device-desktop',
                    'content' => view('admin.pages.bidding.partials.software-ownership')->render(),
                    'onclick' => '()=>{}',
                ],
                [
                    'title' => 'Nhân sự thực hiện',
                    'icon' => 'ti ti-users',
                    'content' => view('admin.pages.bidding.partials.implementation-personnel')->render(),
                    'onclick' => '()=>{}',
                ],
                [
                    'title' => 'Hợp đồng minh chứng / Quyết định giao nhiệm vụ',
                    'icon' => 'ti ti-file-certificate',
                    'content' => view('admin.pages.bidding.partials.proof-contract')->render(),
                    'onclick' => '()=>{}',
                ],
                [
                    'title' => 'Tệp tin khác',
                    'icon' => 'ti ti-folder',
                    'content' => view('admin.pages.bidding.partials.other-files')->render(),
                    'onclick' => '()=>{}',
                ],
                [
                    'title' => 'Tổng hợp kết quả',
                    'icon' => 'ti ti-chart-bar',
                    'content' => view('admin.pages.bidding.partials.result-summary')->render(),
                    'onclick' => '()=>{}',
                ],
            ]" />

        </div>
    </div>
@endsection
@section('modals')
    <x-modal id="confirm-delete-modal" title="Xác nhận xóa" size="sm" :nested="true">
        <x-slot:body>
            <p class="mb-0">Bạn có chắc chắn muốn xóa mục này không?</p>
            <div id="delete-item-info" class="mt-2 text-muted small"></div>
        </x-slot:body>

        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="button" class="btn btn-danger" id="confirm-delete-btn">Xóa</button>
        </x-slot:footer>
    </x-modal>

    <x-modal id="file-selection-modal" title="Chọn file" size="md" :nested="true">
        <x-slot:body>
            <p class="mb-3">Chọn file bạn muốn sử dụng:</p>
            <div id="file-selection-list" class="list-group"></div>
        </x-slot:body>

        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        </x-slot:footer>
    </x-modal>
@endsection
@section('scripts')
    <script>
        const $data = @json($data ?? null);
        const listContractUrl = @json(route('api.contract.list'));
        const listBiddingContractorExperienceUrl = @json(route('api.bidding.contractor-experience.list'));
        const storeBiddingContractorExperienceUrl = @json(route('api.bidding.contractor-experience.store'));
        const deleteBiddingContractorExperienceUrl = @json(route('api.bidding.contractor-experience.delete'));

        const listEligibilitiesUrl = @json(route('api.eligibilities.list'));
        const listProofContractsUrl = @json(route('api.proof_contracts.list'));
        const listSoftwareOwnershipsUrl = @json(route('api.software_ownerships.list'));
        const listPersonnelsUrl = @json(route('api.personnels.list'));
    </script>
    <script src="assets/js/bidding/show/script.js"></script>
    <script type="module" src="assets/js/bidding/show/contractor-experience.js"></script>
    {{-- <script src="assets/js/bidding/show/eligibility.js"></script> --}}
    {{-- <script src="assets/js/bidding/show/software-ownership.js"></script> --}}
    {{-- <script src="assets/js/bidding/show/implementation-personnel.js"></script> --}}
    {{-- <script src="assets/js/bidding/show/proof-contract.js"></script> --}}
    {{-- <script src="assets/js/bidding/show/other-files.js"></script> --}}
@endsection
