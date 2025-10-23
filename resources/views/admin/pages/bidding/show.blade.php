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
                    'onclick' => 'tabBiddingContractorExperience()',
                ],
                [
                    'title' => 'Tư cách hợp lệ',
                    'icon' => 'ti ti-certificate',
                    'content' => view('admin.pages.bidding.partials.eligibility')->render(),
                    'onclick' => 'tabBiddingEligibility()',
                ],
                [
                    'title' => 'Sở hữu phần mềm',
                    'icon' => 'ti ti-device-desktop',
                    'content' => view('admin.pages.bidding.partials.software-ownership')->render(),
                    'onclick' => 'tabBiddingSoftwareOwnership()',
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
@section('scripts')
    <script>
        const $data = @json($data ?? null);
        const listContractUrl = @json(route('api.contract.list'));
        const listBiddingContractorExperienceUrl = @json(route('api.bidding.contractor-experience.list'));
        const storeBiddingContractorExperienceUrl = @json(route('api.bidding.contractor-experience.store'));
        const deleteBiddingContractorExperienceUrl = @json(route('api.bidding.contractor-experience.delete'));
        const deleteByContractIdBiddingContractorExperienceUrl = @json(route('api.bidding.contractor-experience.delete-by-contract-id'));

        const listEligibilityUrl = @json(route('api.eligibilities.list'));
        const listBiddingEligibilityUrl = @json(route('api.bidding.eligibility.list'));
        const storeBiddingEligibilityUrl = @json(route('api.bidding.eligibility.store'));
        const deleteBiddingEligibilityUrl = @json(route('api.bidding.eligibility.delete'));
        const deleteByEligibilityIdBiddingEligibilityUrl = @json(route('api.bidding.eligibility.delete-by-eligibility-id'));

        const listSoftwareOwnershipsUrl = @json(route('api.software_ownerships.list'));
        const listBiddingSoftwareOwnershipUrl = @json(route('api.bidding.software-ownership.list'));
        const storeBiddingSoftwareOwnershipUrl = @json(route('api.bidding.software-ownership.store'));
        const deleteBiddingSoftwareOwnershipUrl = @json(route('api.bidding.software-ownership.delete'));
        const deleteBySoftwareOwnershipIdBiddingSoftwareOwnershipUrl = @json(route('api.bidding.software-ownership.delete-by-software-ownership-id'));

        const listProofContractsUrl = @json(route('api.proof_contracts.list'));
        const listPersonnelsUrl = @json(route('api.personnels.list'));
    </script>
    <script src="assets/js/bidding/show/script.js"></script>
    <script type="module" src="assets/js/bidding/show/contractor-experience.js"></script>
    <script src="assets/js/bidding/show/eligibility.js"></script>
    <script src="assets/js/bidding/show/software-ownership.js"></script>
    {{-- <script src="assets/js/bidding/show/implementation-personnel.js"></script> --}}
    {{-- <script src="assets/js/bidding/show/proof-contract.js"></script> --}}
    {{-- <script src="assets/js/bidding/show/other-files.js"></script> --}}
@endsection
