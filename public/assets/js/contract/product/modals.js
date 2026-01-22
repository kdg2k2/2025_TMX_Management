const productModal = document.getElementById("product-modal");
const productTable = $(productModal.querySelector("table"));

const importProductModal = document.getElementById("import-product-modal");
const importProductModalForm = importProductModal.querySelector("form");

const inspectionProductModal = document.getElementById(
    "inspection-product-modal",
);
const inspectionProductTable = $(inspectionProductModal.querySelector("table"));

const requestInspectionProductModal = document.getElementById(
    "request-inspection-product-modal",
);
const requestInspectionProductModalForm =
    requestInspectionProductModal.querySelector("form");

const cancelInspectionProductModal = document.getElementById(
    "cancel-inspection-product-modal",
);
const cancelInspectionProductModalForm =
    cancelInspectionProductModal.querySelector("form");

const responseInspectionProductModal = document.getElementById(
    "response-inspection-product-modal",
);
const responseInspectionProductModalForm =
    responseInspectionProductModal.querySelector("form");

const filterContainerClass = "contract-year-filter-container";
var currentBtnProductData = null;

const renderFilterYear = (id = null, setRequired = false, multiple = false) => {
    return `
        <label>Năm hợp đồng</label>
        <select id="${id || ""}" name="${multiple ? "years[]" : "year"}" ${setRequired ? "required" : ""} ${multiple ? "multiple" : ""}></select>
    `;
};

const loadProduct = (btn, params = {}) => {
    destroyDataTable(productTable);
    productTable.html("");

    createDataTableServerSide(
        productTable,
        btn?.dataset?.href,
        btn?.dataset?.type == "main"
            ? [
                  {
                      data: null,
                      title: "Năm",
                      render: (data, type, row) => {
                          return row?.year || "";
                      },
                  },
                  {
                      data: null,
                      title: "Tên sản phẩm",
                      render: (data, type, row) => {
                          return row?.name || "";
                      },
                  },
                  {
                      data: null,
                      title: "Số lượng",
                      render: (data, type, row) => {
                          return row?.quantity || "";
                      },
                  },
                  {
                      data: null,
                      title: "Ghi chú",
                      render: (data, type, row) => {
                          return row?.note || "";
                      },
                  },
                  {
                      data: null,
                      title: "Nhận xét",
                      render: (data, type, row) => {
                          return row?.comment || "";
                      },
                  },
              ]
            : [
                  {
                      data: null,
                      title: "Năm",
                      render: (data, type, row) => {
                          return row?.year || "";
                      },
                  },
                  {
                      data: null,
                      title: "Tên sản phẩm",
                      render: (data, type, row) => {
                          return row?.name || "";
                      },
                  },
                  {
                      data: null,
                      title: "Tên người thực hiện",
                      render: (data, type, row) => {
                          return row?.executor_user_name || "";
                      },
                  },
                  {
                      data: null,
                      title: "Ghi chú",
                      render: (data, type, row) => {
                          return row?.note || "";
                      },
                  },
              ],
        (item) => item,
        {
            paginate: 1,
            contract_id: btn?.dataset?.contract_id,
            ...params,
        },
        (res) => {
            const modalBody = $(productModal.querySelector(".modal-body"));
            const filterId = `contract-year-${btn?.dataset?.type}`;

            if (modalBody.find(`.${filterContainerClass}`))
                modalBody.find(`.${filterContainerClass}`).parent().remove();

            importProductModalForm.querySelector(
                `.${filterContainerClass}`,
            ).innerHTML = "";

            modalBody.prepend(
                `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="${filterContainerClass}">
                        </div>
                        <div class="ms-2">
                            ${
                                createActionBtn(
                                    "warning",
                                    "Import Excel",
                                    (btn?.dataset?.type == "main"
                                        ? apiContractProductMainImport
                                        : apiContractProductIntermediateImport) +
                                        "?contract_id=" +
                                        btn?.dataset?.contract_id,
                                    "loadProduct",
                                    "showImportProductModal",
                                    "ti ti-upload",
                                ) +
                                createActionBtn(
                                    "success",
                                    "Export Excel",
                                    (btn?.dataset?.type == "main"
                                        ? apiContractProductMainExport
                                        : apiContractProductIntermediateExport) +
                                        "?contract_id=" +
                                        btn?.dataset?.contract_id,
                                    "loadProduct",
                                    "exportBtnClick",
                                    "ti ti-download",
                                )
                            }
                        </div>
                    </div>
                `,
            );

            if (res.years.length > 0) {
                modalBody
                    .find(`.${filterContainerClass}`)
                    .append(renderFilterYear(filterId));
                importProductModalForm.querySelector(
                    `.${filterContainerClass}`,
                ).innerHTML = renderFilterYear(null, true);

                document
                    .querySelectorAll(`.${filterContainerClass} select`)
                    .forEach((element) => {
                        fillSelectElement(
                            element,
                            res.years,
                            "year",
                            "year",
                            "[Chọn]",
                            true,
                            params?.year || null,
                        );
                    });

                $(`#${filterId}`)
                    .off("change")
                    .on("change", (e) => {
                        loadProduct(btn, { year: e.target.value });
                    });
            }
        },
    );
};

const showProductModal = (btn) => {
    openModalBase(btn, {
        modal: productModal,
        afterShow: () => {
            currentBtnProductData = btn;
            loadProduct(btn);
        },
    });
};

const exportBtnClick = async (btn) => {
    const res = await http.get(btn.dataset.href);
    if (res?.data) downloadFileHandler(res.data);
};

const showImportProductModal = (btn) => {
    openModalBase(btn, {
        modal: importProductModal,
        form: importProductModalForm,
    });
};

const showInspectionProductModal = (btn) => {
    openModalBase(btn, {
        modal: inspectionProductModal,
        afterShow: () => {
            currentBtnProductData = btn;
            loadInspectionProduct(btn);
        },
    });
};

window.loadInspectionProduct = (btn = null) => {
    if (!btn) btn = currentBtnProductData;
    destroyDataTable(inspectionProductTable);
    inspectionProductTable.html("");

    createDataTableServerSide(
        inspectionProductTable,
        btn?.dataset?.href,
        [
            {
                data: null,
                title: "Yêu cầu kiểm tra",
                render: (data, type, row) => `
                    <div class="d-flex align-items-start mb-1">
                        <i class="ti ti-user-question text-primary me-1 mt-1"></i>
                        <div>
                            <span class="text-muted">Người yêu cầu/hủy:</span>
                            <span class="fw-medium">${row?.created_by?.name || "-"}</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-1">
                        <i class="ti ti-calendar text-warning me-1 mt-1"></i>
                        <div>
                            <span class="text-muted">Năm:</span>
                            <span class="fw-medium">${
                                row?.years
                                    ?.map((i) => i?.year)
                                    ?.filter(Boolean)
                                    ?.join(", ") || "-"
                            }</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-1">
                        <i class="ti ti-user-cog text-info me-1 mt-1"></i>
                        <div>
                            <span class="text-muted">Hỗ trợ:</span>
                            ${row?.supported_by?.name || "-"}
                        </div>
                    </div>

                    <div class="d-flex align-items-start small text-muted mt-1">
                        <i class="ti ti-message-dots me-1 mt-1"></i>
                        <div>
                            <span class="fw-medium">Mô tả:</span>
                            ${row?.support_description || "-"}
                        </div>
                    </div>

                    ${
                        row?.issue_file_path
                            ? `<div class="d-flex align-items-center small text-muted mt-1">
                                <i class="ti ti-file-search me-1"></i>
                                <span class="fw-medium me-1">File tồn tại:</span>
                                ${createViewBtn(row.issue_file_path)}
                            </div>`
                            : ""
                    }
                `,
            },
            {
                data: null,
                title: "Trạng thái",
                render: (data, type, row) =>
                    createBadge(
                        row?.status?.converted,
                        row?.status?.color,
                        row?.status?.icon,
                    ),
            },
            {
                data: null,
                title: "Kết quả kiểm tra",
                render: (data, type, row) => `
                    <div class="d-flex align-items-start mb-1">
                        <i class="ti ti-user-check text-success me-1 mt-1"></i>
                        <div>
                            <span class="text-muted">Người kiểm tra:</span>
                            <span class="fw-medium">${row?.inspector_user?.name || "-"}</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-start small text-muted mt-1">
                        <i class="ti ti-note text-secondary me-1 mt-1"></i>
                        <div>
                            <span class="text-muted">Nhận xét:</span>
                            <span class="fw-medium">${row.inspector_comment || "-"}</span>
                        </div>
                    </div>

                    ${
                        row?.inspector_comment_file_path
                            ? `<div class="d-flex align-items-center small text-muted mt-1">
                                <i class="ti ti-file-check me-1"></i>
                                <span class="fw-medium me-1">File nhận xét:</span>
                                ${createViewBtn(row.inspector_comment_file_path)}
                            </div>`
                            : ""
                    }
                `,
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                title: "Hành động",
                className: "text-center",
                render: (data, type, row) => {
                    return `
                        ${
                            row.is_inspection_created_by_auth
                                ? createActionBtn(
                                    "danger",
                                    "Hủy yêu cầu kiểm tra",
                                    `${apiContractProductInspectionCancel}?id=${row.id}`,
                                    "loadInspectionProduct",
                                    "showCancelInspectionProductModal",
                                    "ti ti-ban",
                                )
                                : ""
                        }
                        ${
                            row.is_auth_inspector
                                ? createActionBtn(
                                    "secondary",
                                    "Phản hồi kiểm tra",
                                    `${apiContractProductInspectionResponse}?id=${row.id}`,
                                    "loadInspectionProduct",
                                    "showResponseInspectionProductModal",
                                    "ti ti-clipboard-check",
                                )
                                : ""
                        }
                    `;
                },
            },
        ],
        (item) => item,
        {
            paginate: 1,
            contract_id: btn?.dataset?.contract_id,
        },
        () => {
            $(
                inspectionProductModal.querySelector(".dataTables_filter"),
            ).prepend(
                createActionBtn(
                    "warning",
                    "Yêu cầu kiểm tra",
                    `${apiContractProductInspectionRequest}?contract_id=${btn?.dataset?.contract_id}`,
                    "loadInspectionProduct",
                    "showRequestInspectionProductModal",
                    "ti ti-clipboard-search",
                    {
                        "data-contract_id": btn?.dataset?.contract_id,
                    },
                ),
            );
        },
    );
};

const showRequestInspectionProductModal = async (btn) => {
    const years = await http.get(apiContractProductContractYears, {
        contract_id: btn.dataset.contract_id,
    });

    requestInspectionProductModalForm.querySelector(
        `.${filterContainerClass}`,
    ).innerHTML =
        years?.data?.length > 0 ? renderFilterYear(null, true, true) : "";
    if (years?.data?.length > 0)
        fillSelectElement(
            requestInspectionProductModalForm.querySelector(
                `.${filterContainerClass} select`,
            ),
            years.data,
            "year",
            "year",
            null,
            false,
        );

    resetFormAfterSubmit(requestInspectionProductModalForm);
    openModalBase(btn, {
        modal: requestInspectionProductModal,
        form: requestInspectionProductModalForm,
    });
};

const showCancelInspectionProductModal = (btn) => {
    resetFormAfterSubmit(cancelInspectionProductModalForm);
    openModalBase(btn, {
        modal: cancelInspectionProductModal,
        form: cancelInspectionProductModalForm,
    });
};

const showResponseInspectionProductModal = (btn) => {
    resetFormAfterSubmit(responseInspectionProductModalForm);
    openModalBase(btn, {
        modal: responseInspectionProductModal,
        form: responseInspectionProductModalForm,
    });
};

[
    importProductModalForm,
    requestInspectionProductModalForm,
    cancelInspectionProductModalForm,
    responseInspectionProductModalForm,
].forEach((form) => {
    form.addEventListener("submit", async (e) => {
        await handleSubmitForm(e, () => {
            hideModal(form.closest(".modal"));
            loadList();

            if (form == importProductModalForm)
                loadProduct(currentBtnProductData);
            if (form == requestInspectionProductModalForm)
                loadInspectionProduct(currentBtnProductData);
        });
    });
});
