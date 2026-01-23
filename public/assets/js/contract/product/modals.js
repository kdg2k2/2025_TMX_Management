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

const minuteProductModal = document.getElementById("minute-product-modal");
const minuteProductTable = $(minuteProductModal.querySelector("table"));

const createMinuteProductModal = document.getElementById(
    "create-minute-product-modal",
);
const createMinuteProductModalForm =
    createMinuteProductModal.querySelector("form");

const replaceMinuteFileModal = document.getElementById(
    "replace-minute-file-modal",
);
const replaceMinuteFileModalForm = replaceMinuteFileModal.querySelector("form");

const requestSignMinuteModal = document.getElementById(
    "request-sign-minute-modal",
);
const requestSignMinuteModalForm = requestSignMinuteModal.querySelector("form");

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
                      title: "Số hợp đồng",
                      render: (data, type, row) => {
                          return row?.contract_number || "";
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
                render: (data, type, row) => {
                    const years =
                        row?.years
                            ?.map((i) => i?.year)
                            ?.filter(Boolean)
                            ?.join(", ") || "-";

                    return `
                        ${renderField("user-question", "Người yêu cầu", row?.created_by?.name, { color: "primary" })}
                        ${renderField("calendar", "Năm", years, { color: "warning" })}
                        ${renderField("user-cog", "Hỗ trợ", row?.supported_by?.name, { color: "info" })}
                        ${renderField("message-dots", "Mô tả", row?.support_description)}

                        ${
                            row?.issue_file_path
                                ? renderField(
                                      "file-search",
                                      "File tồn tại",
                                      createViewBtn(row.issue_file_path),
                                      { valueClass: "" },
                                  )
                                : ""
                        }
                    `;
                },
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
                render: (data, type, row) => {
                    return `
                        ${renderField("user-check", "Người kiểm tra", row?.inspector_user?.name, { color: "success" })}
                        ${renderField("note", "Nhận xét", row?.inspector_comment)}

                        ${
                            row?.inspector_comment_file_path
                                ? renderField(
                                      "file-check",
                                      "File nhận xét",
                                      createViewBtn(
                                          row.inspector_comment_file_path,
                                      ),
                                  )
                                : ""
                        }
                    `;
                },
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
    const years = await http.get(apiContractManyYearList, {
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

const showMinuteProductModal = (btn) => {
    showIframeMinute();
    openModalBase(btn, {
        modal: minuteProductModal,
        afterShow: () => {
            currentBtnProductData = btn;
            loadMinuteProduct(btn);
        },
    });
};

const showCreateMinuteProductModal = async (btn) => {
    const professionals = await http.get(apiContractProfessionalList, {
        contract_id: btn.dataset.contract_id,
    });
    const disbursements = await http.get(apiContractDisbursementList, {
        contract_id: btn.dataset.contract_id,
    });
    fillSelectId(
        "professional-user-id",
        professionals.data,
        "user.id",
        "user.name",
        null,
        false,
    );
    fillSelectId(
        "disbursement-user-id",
        disbursements.data,
        "user.id",
        "user.name",
        null,
        false,
    );

    resetFormAfterSubmit(createMinuteProductModalForm);

    const signDate = new Date(btn.dataset.contract_signed_date);
    createMinuteProductModal.querySelector(
        'textarea[name="legal_basis"]',
    ).value =
        `Căn cứ hợp đồng số ${btn.dataset.contract_number} ngày ${signDate.getDate()} tháng ${signDate.getMonth() + 1} năm ${signDate.getFullYear()} hợp đồng`;

    openModalBase(btn, {
        modal: createMinuteProductModal,
        form: createMinuteProductModalForm,
    });
};

const showIframeMinute = (url = "") => {
    minuteProductModal
        .querySelector("iframe")
        .setAttribute("src", url ? createLinkPreviewFileOnline(url) : "");
};

const showReplaceMinuteFileModal = (btn) => {
    resetFormAfterSubmit(replaceMinuteFileModalForm);
    openModalBase(btn, {
        modal: replaceMinuteFileModal,
        form: replaceMinuteFileModalForm,
    });
};

const showRequestSignMinuteModal = (btn) => {
    resetFormAfterSubmit(requestSignMinuteModalForm);
    openModalBase(btn, {
        modal: requestSignMinuteModal,
        form: requestSignMinuteModalForm,
    });
};

var minuteProductBtnAdded = false;

// Helper function để render action buttons cho minute product
const renderMinuteProductActions = (row) => {
    const status = row?.status?.original;
    const buttons = [];

    // Button xem file Word
    if (row?.file_docx_path) {
        buttons.push(
            createDropdownBtn(
                "info",
                "File Word",
                [
                    {
                        text: "Xem",
                        icon: "ti ti-eye",
                        onClick: `showIframeMinute(${JSON.stringify(row.file_docx_path)})`,
                    },
                    {
                        divider: true,
                    },
                    {
                        text: "Tải",
                        icon: "ti ti-download",
                        onClick: `downloadFileHandler(${JSON.stringify(row.file_docx_path)})`,
                    },
                ],
                "ti ti-file-word",
            )?.outerHTML || "",
        );
    }

    // Button xem file PDF
    if (row?.file_pdf_path) {
        buttons.push(
            createDropdownBtn(
                "warning",
                "File PDF",
                [
                    {
                        text: "Xem",
                        icon: "ti ti-eye",
                        onClick: `showIframeMinute(${JSON.stringify(row.file_pdf_path)})`,
                    },
                    {
                        divider: true,
                    },
                    {
                        text: "Tải",
                        icon: "ti ti-download",
                        onClick: `downloadFileHandler(${JSON.stringify(row.file_pdf_path)})`,
                    },
                ],
                "ti ti-file-type-pdf",
            )?.outerHTML || "",
        );
    }

    // Button ghi đè file Word
    if (["draft", "request_sign"].includes(status)) {
        buttons.push(
            createActionBtn(
                "warning",
                "Ghi đè file Word",
                `${apiContractProductMinuteReplace}?id=${row.id}`,
                "loadMinuteProduct",
                "showReplaceMinuteFileModal",
                "ti ti-file-upload",
            ),
        );
    }

    // Button yêu cầu ký
    if (status === "draft") {
        buttons.push(
            createActionBtn(
                "primary",
                "Yêu cầu ký",
                `${apiContractProductMinuteSignatureRequest}?id=${row.id}`,
                "loadMinuteProduct",
                "showRequestSignMinuteModal",
                "ti ti-signature",
            ),
        );
    }

    // Button mở trang ký (khi đang yêu cầu ký)
    if (status === "request_sign") {
        buttons.push(
            createActionBtn(
                "success",
                "Mở trang ký",
                null,
                null,
                `window.open('${contractProductMinuteSignPage}?minute_id=${row.id}', '_blank')`,
                "ti ti-external-link",
            ),
        );
    }

    // Button duyệt/từ chối
    if (status === "request_approve") {
        buttons.push(
            createApproveBtn(`${contractProductMinuteApprove}?contract_id=${row.contract_id}`),
            createRejectBtn(`${contractProductMinuteReject}?contract_id=${row.contract_id}`),
        );
    }

    return buttons.join("");
};

window.loadMinuteProduct = (btn = null) => {
    if (!btn) btn = currentBtnProductData;
    destroyDataTable(minuteProductTable);
    minuteProductTable.html("");
    minuteProductBtnAdded = false;

    createDataTableServerSide(
        minuteProductTable,
        btn?.dataset?.href,
        [
            {
                data: null,
                title: "Thông tin",
                render: (data, type, row) => {
                    return `
                    ${renderField("user", "Người tạo", row?.created_by?.name)}
                    ${renderField("calendar", "Ngày giao", row?.handover_date)}
                    <div class="d-flex align-items-center gap-1 mt-1">
                        <i class="ti ti-flag text-muted"></i>
                        <small class="text-muted">Trạng thái:</small>
                        ${createBadge(
                            row?.status?.converted,
                            row?.status?.color,
                            row?.status?.icon,
                        )}
                    </div>
                `;
                },
            },

            {
                data: null,
                title: "Nội dung",
                width: "350px",
                render: (data, type, row) => {
                    return `
                    ${renderField("file-text", "Căn cứ", row?.legal_basis)}
                    ${renderField("clipboard-list", "Bàn giao", row?.handover_content)}
                    ${renderField("alert-circle", "Tồn tại", row?.issue_note)}
                    ${renderField("user-star", "Chuyên môn", row?.professional_user?.name, { color: "primary" })}
                    ${renderField("user-dollar", "Giải ngân", row?.disbursement_user?.name, { color: "success" })}
                `;
                },
            },

            {
                data: null,
                title: "Người ký",
                width: "200px",
                render: (data, type, row) => {
                    const signatures = row?.signatures || [];
                    if (signatures.length === 0) {
                        return '<small class="text-muted">Chưa có người ký</small>';
                    }

                    return signatures
                        .map((sig) => {
                            const statusOriginal =
                                sig?.status?.original || sig.status;
                            const isSigned = statusOriginal === "signed";
                            const icon = isSigned
                                ? '<i class="ti ti-circle-check text-success" title="Đã ký"></i>'
                                : '<i class="ti ti-clock-hour-4 text-warning" title="Chờ ký"></i>';
                            return `
                            <div class="d-flex align-items-center gap-1 mb-1">
                                ${icon}
                                <small>${sig?.user?.name || "N/A"}</small>
                            </div>
                        `;
                        })
                        .join("");
                },
            },

            {
                data: null,
                title: "Phê duyệt",
                render: (data, type, row) => {
                    return `
                    ${renderField("user-check", "Người duyệt", row?.approved_by?.name)}
                    ${renderField("clock", "Thời gian", row?.approved_at)}
                    ${renderField(
                        "message",
                        "Ghi chú",
                        row?.approval_note || row?.rejection_note,
                    )}
                `;
                },
            },

            {
                data: null,
                orderable: false,
                searchable: false,
                title: "Hành động",
                className: "text-center",
                render: (data, type, row) => renderMinuteProductActions(row),
            },
        ],
        (item) => item,
        {
            paginate: 1,
            contract_id: btn?.dataset?.contract_id,
        },
        () => {
            if (minuteProductBtnAdded) return;
            minuteProductBtnAdded = true;

            $(minuteProductModal.querySelector(".dataTables_filter")).prepend(
                createActionBtn(
                    "primary",
                    "Tạo biên bản",
                    `${apiContractProductMinuteCreate}?contract_id=${btn?.dataset?.contract_id}`,
                    "loadMinuteProduct",
                    "showCreateMinuteProductModal",
                    "ti ti-file-plus",
                    {
                        "data-contract_id": btn?.dataset?.contract_id,
                        "data-contract_number": btn?.dataset?.contract_number,
                        "data-contract_signed_date":
                            btn?.dataset?.contract_signed_date,
                    },
                ),
            );
        },
    );
};

// Form submit handlers - đăng ký event listeners cho từng form
const registerFormHandler = (form, handler) => {
    form.addEventListener("submit", async (e) => {
        await handleSubmitForm(e, (res) => {
            hideModal(form.closest(".modal"));
            loadList();

            // Gọi handler tương ứng nếu có
            if (typeof handler === "function") {
                handler(res);
            }
        });
    });
};

// Import product form
registerFormHandler(importProductModalForm, () => {
    loadProduct(currentBtnProductData);
});

// Request inspection form
registerFormHandler(requestInspectionProductModalForm, () => {
    loadInspectionProduct(currentBtnProductData);
});

// Create minute form
registerFormHandler(createMinuteProductModalForm, (res) => {
    showIframeMinute(
        res?.data?.file_pdf_path || res?.data?.file_docx_path || "",
    );
});

// Replace minute file form
registerFormHandler(replaceMinuteFileModalForm, (res) => {
    loadMinuteProduct();
    showIframeMinute(
        res?.data?.file_pdf_path || res?.data?.file_docx_path || "",
    );
});

// Request signature form
registerFormHandler(requestSignMinuteModalForm, () => {
    loadMinuteProduct();
});

// Cancel inspection form
registerFormHandler(cancelInspectionProductModalForm, () => {});

// Response inspection form
registerFormHandler(responseInspectionProductModalForm, () => {});
