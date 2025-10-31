// Cấu hình mặc định cho DataTables
const DEFAULT_DATATABLE_CONFIG = {
    processing: true,
    responsive: {
        details: {
            type: "column",
            target: 0,
        },
        // BẮT BUỘC ẨN các cột này vào responsive
        orthogonal: "responsive",
    },
    lengthChange: true,
    autoWidth: false,
    ordering: false,
    searching: true,
    lengthMenu: [
        [10, 30],
        [10, 30],
    ],
    bLengthChange: true,
    columnDefs: [
        {
            className: "dtr-control",
            orderable: false,
            targets: 0,
        },
        {
            className: "all", // luôn hiển thị
            targets: [1, 2, 3, 4, 5, 6, -1],
        },
    ],
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

    // Đếm số cột trong table
    let columnCount = 0;
    if (additionalConfig.columns && Array.isArray(additionalConfig.columns)) {
        columnCount = additionalConfig.columns.length;
    } else {
        columnCount = element.find("thead th").length;
    }

    // Chỉ áp dụng responsive config nếu có nhiều hơn 10 cột
    let baseConfig = { ...DEFAULT_DATATABLE_CONFIG };

    if (columnCount <= 10) {
        // Xóa columnDefs và chuyển responsive về true
        delete baseConfig.columnDefs;
        baseConfig.responsive = true;
    } else {
        // Tự động tính toán targets none dựa trên targets all
        const allTargets =
            baseConfig.columnDefs.find((def) => def.className === "all")
                ?.targets || [];
        const noneTargets = [];

        for (let i = 1; i < columnCount; i++) {
            // Bắt đầu từ 1 thay vì 0
            if (!allTargets.includes(i) && i !== columnCount - 1) {
                // Bỏ qua cột cuối (vì có -1)
                noneTargets.push(i);
            }
        }

        // Cập nhật lại columnDefs với targets none tự động
        baseConfig.columnDefs = [
            {
                className: "dtr-control",
                orderable: false,
                targets: 0,
            },
            {
                className: "all",
                targets: allTargets,
            },
            {
                className: "none",
                targets: noneTargets,
            },
        ];
    }

    // Merge config mặc định với config bổ sung
    const config = {
        ...baseConfig,
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

    // Gắn event checkbox SAU KHI khởi tạo DataTable
    if (
        additionalConfig.onCheckboxChange &&
        typeof additionalConfig.onCheckboxChange === "function"
    ) {
        // Sử dụng event delegation để tránh mất event sau khi redraw
        element.on("change", ".row-checkbox", function () {
            const row = $(this).closest("tr");
            const data = dataTable.row(row).data();
            additionalConfig.onCheckboxChange(this.checked, data, this);
        });
    }

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
    callbackAfterRender = () => {},
    enableCheckbox = false,
    onCheckboxChange = null
) => {
    var serverResponse = null;

    // Thêm cột checkbox vào đầu nếu enableCheckbox = true
    const finalColumns = enableCheckbox
        ? [
              {
                  data: null,
                  orderable: false,
                  searchable: false,
                  className: "text-center",
                  width: "50px",
                  render: (data, type, row) => {
                      return `<input type="checkbox" class="row-checkbox" data-id="${
                          row.id || ""
                      }">`;
                  },
              },
              ...columns,
          ]
        : columns;

    const serverSideConfig = {
        serverSide: true,
        columns: finalColumns,
        onCheckboxChange: onCheckboxChange,
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
            // Gọi callbackAfterRender SAU KHI data đã được load
            if (typeof callbackAfterRender === "function" && serverResponse)
                callbackAfterRender(serverResponse);
        },
    };

    const dataTable = initializeBaseDataTable(element, serverSideConfig);
    debounceSearch(element, dataTable);

    return dataTable;
};
