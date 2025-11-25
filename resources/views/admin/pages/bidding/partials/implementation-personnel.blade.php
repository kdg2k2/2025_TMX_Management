<div class="card">
    <div class="card-header fw-bold text-primary">
        Chọn nhân sự
    </div>
    <div class="card-body">
        <form id="implementation-personnel-form"
            action="{{ route('api.bidding.implementation-personnel.store') }}">
            @method('post')
            <div class="row clone-container">
                <div class="col-12 row" id="implementation-personnel-clone-row">
                    <input type="hidden" name="personnels[0][bidding_id]" value="{{ $data['id'] }}">
                    <div class="col-lg-3 col-md-6">
                        <div class="my-1">
                            <label>
                                Đơn vị
                            </label>
                            <select class="implementation-personnel-unit" required></select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="my-1">
                            <label>
                                Nhân sự
                            </label>
                            <select class="implementation-personnel" name="personnels[0][personnel_id]"
                                required></select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="my-1">
                            <label>
                                Bằng cấp
                            </label>
                            <select class="implementation-personnel-file" name="personnels[0][files][]" multiple
                                required></select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="my-1">
                            <label>
                                Chức danh
                            </label>
                            <select class="implementation-personnel-jobtitle" name="personnels[0][job_title]" required>
                                <x-select-options :items="$biddingimplementationPersonnelJobtitles" keyField="original" valueFields="converted" />
                            </select>
                        </div>
                    </div>
                    <div class="col-1 d-flex justify-content-center align-items-end">
                        <div class="my-1">
                            <x-button icon="ti ti-plus" variant="success" size="sm" tooltip="Thêm dòng"></x-button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="my-1 text-center btn-submit-row">
                <x-button-submit />
            </div>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-header fw-bold text-success">
        Nhân sự thực hiện
    </div>
    <div class="card-body">
        <table class="display w-100" id="table-implementation-personnel"></table>
    </div>
</div>
