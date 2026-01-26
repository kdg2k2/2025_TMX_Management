<div class="row mb-2">
    <div class="col-lg-2 my-1">
        <label>
            Nhân sự
        </label>
        <select id="personnel-id">
            <x-select-options :items="$personnels" :emptyOption="false" />
        </select>
    </div>
    <div class="col-lg-2 my-1">
        <label>
            Năm hợp đồng
        </label>
        <select id="year-contract">
            <x-select-options :items="$years" />
        </select>
    </div>
    <div class="col-lg-3 my-1">
        <label>
            Chủ đầu tư
        </label>
        <select id="investor-id">
            <x-select-options :items="$investors" :valueFields="['name_vi', 'name_en']" />
        </select>
    </div>
    <div class="col-lg-4 my-1">
        <label>
            Hợp đồng
        </label>
        <select id="contract-id">
        </select>
    </div>
    <div class="col-lg-1 my-1 d-flex justify-content-center align-items-center">
        <x-button icon="ti ti-filter" tooltip="Áp dụng lọc" id="filter" class="me-1" />
        <x-button icon="ti ti-download" tooltip="Tải excel" id="download" variant="success" />
    </div>
</div>
<iframe src="" id="synthetic-iframe" class="w-100 bg-light" frameborder="0" style="height: 60vh;"></iframe>
