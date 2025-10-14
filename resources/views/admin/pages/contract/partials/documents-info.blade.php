<div class="card mb-2">
    <div class="card-body row">
        <div class="col-md-3">
            <select id="document-type-filter">
                <x-select-options :items="$fileTypes" emptyText="Loại file" keyField="id" :valueFields="['name']" />
            </select>
        </div>
        <div class="col-md-9 d-flex justify-content-end">
            <x-button type="button" variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới"
                onclick="openCreateFileModal()" />
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <table class="display w-100" id="documents-info-datatable"></table>
    </div>
</div>
