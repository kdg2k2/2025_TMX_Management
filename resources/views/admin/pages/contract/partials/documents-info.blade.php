<div class="row mb-2">
        <div class="col-lg-3 col-mđ-6">
            <select id="document-type-filter">
                <x-select-options :items="$fileTypes" emptyText="Loại file" keyField="id" :valueFields="['name']" />
            </select>
        </div>
        <div class="col-lg-9 col-mđ-6 d-flex justify-content-end">
            <x-button type="button" variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới"
                onclick="openCreateFileModal()" />
        </div>
    </div>
<div class="card m-0">
    <div class="card-body">
        <table class="display w-100" id="documents-info-datatable"></table>
    </div>
</div>
