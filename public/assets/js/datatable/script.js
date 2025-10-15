// Cấu hình mặc định cho DataTables
const DEFAULT_DATATABLE_CONFIG = {
    processing: true,
    responsive: true,
    lengthChange: true,
    autoWidth: false,
    ordering: false,
    searching: true,
    lengthMenu: [
        [10, 30],
        [10, 30],
    ],
    bLengthChange: true,
    language: {
        sLengthMenu: "Hiển thị _MENU_ bản ghi",
        searchPlaceholder: "Nhập từ khóa...",
        info: "Từ _START_ đến _END_ | Tổng số _TOTAL_",
        sInfoEmpty: "Không có dữ liệu",
        sEmptyTable: "Không có dữ liệu",
        sSearch: "Tìm kiếm",
        sZeroRecords: "Không tìm thấy dữ liệu phù hợp",
        sInfoFiltered: "",
        paginate: {
            previous: '<i class="ti ti-chevron-left"></i>',
            next: '<i class="ti ti-chevron-right"></i>',
        },
    },
};

// Hàm khởi tạo các event cho tooltip
const initializeTooltipEvents = (dataTable) => {
    // Khởi tạo tooltips sau mỗi lần vẽ lại bảng
    dataTable.on("draw.dt page.dt search.dt length.dt", function () {
        setTimeout(() => {
            initializeTooltips();
        }, 100);
    });
};

// Hàm base để khởi tạo DataTable
const initializeBaseDataTable = (element, additionalConfig = {}) => {
    // Hủy DataTable cũ nếu tồn tại
    destroyDataTable(element);

    // Merge config mặc định với config bổ sung
    const config = {
        ...DEFAULT_DATATABLE_CONFIG,
        drawCallback: function () {
            initializeTooltips();
        },
        initComplete: function () {
            initializeTooltips();
        },
        ...additionalConfig,
    };

    // Khởi tạo DataTable
    const dataTable = element.DataTable(config);

    // Khởi tạo các event cho tooltip
    initializeTooltipEvents(dataTable);

    return dataTable;
};

// Hàm hủy DataTable
const destroyDataTable = (element) => {
    if ($.fn.DataTable.isDataTable(element)) {
        element.DataTable().destroy();
    }
};

// Hàm debounce cho tìm kiếm
const debounceSearch = (tableElement, dataTable, delay = 1500) => {
    const domTable = tableElement[0];
    const wrapper = domTable.closest(".dataTables_wrapper");
    if (!wrapper) return;

    const input = wrapper.querySelector("input[type=search]");
    if (!input) return;

    $(input).off("input.DT").off("keyup.DT");

    let debounceTimer;
    let previousValue = input.value;

    input.removeEventListener("input", input._debouncedHandler ?? (() => {}));

    const handler = function () {
        const currentValue = this.value;

        // Nếu giá trị hiện tại giống giá trị trước đó, không cần search lại
        if (currentValue === previousValue) {
            return;
        }

        // Nếu giá trị rỗng và giá trị trước đó cũng rỗng, không cần search
        if (currentValue === "" && previousValue === "") {
            return;
        }

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            previousValue = currentValue;
            dataTable.search(currentValue).draw();
        }, delay);
    };

    input.addEventListener("input", handler);
    input._debouncedHandler = handler;
};

// Hàm khởi tạo DataTable client-side
const initDataTable = (element) => {
    return initializeBaseDataTable(element);
};

// Hàm khởi tạo DataTable server-side
const createDataTableServerSide = (
    element,
    apiUrl,
    columns,
    mapFn,
    extraParams = {},
    func = () => {}
) => {
    var serverResponse = null;
    const serverSideConfig = {
        serverSide: true,
        columns,
        ajax: (data, callback) => {
            const page = Math.floor(data.start / data.length) + 1;
            const perPage = data.length;
            const search = data.search.value;

            http.get(
                apiUrl,
                {
                    ...extraParams,
                    page,
                    per_page: perPage,
                    search: search,
                },
                null,
                true
            ).then((res) => {
                serverResponse = res;
                const items = res?.data?.data ?? [];
                callback({
                    data: items.map(mapFn),
                    recordsTotal: res.data.total,
                    recordsFiltered: res.data.total,
                });
            });
        },
        drawCallback: function () {
            // Gọi func SAU KHI data đã được load
            if (typeof func === "function" && serverResponse)
                func(serverResponse);
        },
    };

    const dataTable = initializeBaseDataTable(element, serverSideConfig);
    debounceSearch(element, dataTable);

    return dataTable;
};
