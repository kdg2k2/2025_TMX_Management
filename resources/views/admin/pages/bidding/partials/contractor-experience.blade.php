<div class="row g-1">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between w-100">
                    <div class="fw-bold text-primary">
                        Dữ liệu gốc
                    </div>
                    <div>
                        Bản HĐ khi thêm
                        <select name="file_type" class="un-sumo">
                            <x-select-options :items="$biddingContractorExperienceFileTypes" :emptyOption="false" keyField="original"
                                valueFields="converted" />
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="display w-100" id="original-contractor-experience"></table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-bold text-success">
                Dữ liệu được chọn
            </div>
            <div class="card-body">
                <table class="display w-100" id="selected-contractor-experience"></table>
            </div>
        </div>
    </div>
</div>
