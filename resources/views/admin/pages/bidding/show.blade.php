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
                    'content' => view('admin.pages.bidding.partials.contractor-experience')->render(),
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
                    'content' => view('admin.pages.bidding.partials.proof-contracts')->render(),
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
@section('scripts')
    <script>
        const $data = @json($data ?? null);
        const listContractUrl = @json(route('api.contract.list'));
    </script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js"></script>
    <script src="assets/js/http-request/base-store-and-update.js"></script>
    <script src="assets/js/bidding/show/script.js"></script>
    <script src="assets/js/bidding/show/contractor-experience.js"></script>
    <script src="assets/js/bidding/show/eligibility.js"></script>
    <script src="assets/js/bidding/show/software-ownership.js"></script>
    <script src="assets/js/bidding/show/implementation-personnel.js"></script>
    <script src="assets/js/bidding/show/proof-contracts.js"></script>
    <script src="assets/js/bidding/show/other-files.js"></script>
@endsection
