<div class="card">
    <div class="card-header fw-bold text-primary">
        Upload bổ sung
    </div>
    <div class="card-body">
        <form id="orther-file-form" action="{{ route('api.bidding.orther-file.store') }}" enctype="multipart/form-data">
            @method('post')
            <div class="row clone-container">
                <div class="col-12 row" id="orther-file-clone-row">
                    <input type="hidden" name="orther_file[0][bidding_id]" value="{{ $data['id'] }}">
                    <div class="col-lg-6 col-md-6">
                        <div class="my-1">
                            <label>
                                Nội dung
                            </label>
                            <input type="text" class="form-control" name="orther_file[0][content]" required>
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-6">
                        <div class="my-1">
                            <label>
                                File(pdf,docx,xlsx)
                            </label>
                            <input type="file" class="form-control" name="orther_file[0][path]"
                                accept=".pdf,.docx,.xlsx" required>
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
        Các file đã upload
    </div>
    <div class="card-body">
        <table class="display w-100" id="table-orther-file"></table>
    </div>
</div>
