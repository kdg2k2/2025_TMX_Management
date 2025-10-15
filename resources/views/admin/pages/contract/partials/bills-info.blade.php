<div class="card mb-2">
    <div class="card-body row">
        <div class="col-md-3">
            <select id="bill-type-filter">
                <x-select-options :items="$users" emptyText="Người phụ trách lấy" keyField="id" :valueFields="['name']" />
            </select>
        </div>
        <div class="col-md-9 d-flex justify-content-end">
            <x-button type="button" variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới"
                onclick="openBillModal()" />
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <table class="display w-100" id="bills-info-datatable"></table>
    </div>
</div>
