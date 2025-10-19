const scanDocumentsInfoDatatable = document.getElementById(
    "scan-documents-info-datatable"
);
const createContractScanFileModal = document.getElementById(
    "create-contract-scan-file-modal"
);
const createContractScanFileModalForm =
    createContractScanFileModal.querySelector("form");
const contractScanFileType = document.getElementById("contract-scan-file-type");
const contractScanFileLabel = document.getElementById(
    "contract-scan-file-label"
);
const contractScanFileInput = document.getElementById(
    "contract-scan-file-input"
);
const scanDocumentTypeFilter = document.getElementById("document-type-filter");

window.renderScanDocumentsInfo = () => {
    createDataTableServerSide(
        $(scanDocumentsInfoDatatable),
        listScanFileUrl,
        renderScanDocumentsInfoColumns(),
        (item) => item,
        getScanDocumentsInfoFilterParams(),
        (response) => {
            $("#scan-document-count").text(response.data.total);
        }
    );
};

const getScanDocumentsInfoFilterParams = () => {
    const params = {
        paginate: 1,
        contract_id: contractId,
    };

    if (scanDocumentTypeFilter.value)
        params["type_id"] = scanDocumentTypeFilter.value;

    return params;
};

const renderScanDocumentsInfoColumns = () => {
    return [
        {
            data: null,
            title: "Loại file",
            render: (data, type, row) => {
                return row?.type?.name;
            },
        },
        {
            data: null,
            title: "Người đăng tải",
            render: (data, type, row) => {
                return row?.created_by?.name;
            },
        },
        {
            data: null,
            title: "Thời gian tạo",
            render: (data, type, row) => {
                return `
                    <ul class="m-0">
                        <li>${row.created_at}</li>
                        <li>${row.updated_at}</li>
                    </ul>
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
                return renderScanDocumentsInfoActionButtons(row);
            },
        },
    ];
};

const renderScanDocumentsInfoActionButtons = (row) => {
    return `
        ${
            createBtn(
                "info",
                "Xem",
                false,
                {},
                "ti ti-eye-search",
                `viewFileHandler('${row.path}')`
            )?.outerHTML
        }
        ${
            createBtn(
                "success",
                "Tải",
                false,
                {},
                "ti ti-download",
                `window.open('${row.path}', "_blank")`
            )?.outerHTML
        }
        ${
            createBtn(
                "warning",
                "Cập nhật",
                false,
                {},
                "ti ti-edit",
                `openScanFileModal("patch","${updateScanFileUrl}?id=${
                    row.id
                }", ${JSON.stringify(row)})`
            )?.outerHTML
        }
        ${createDeleteBtn(
            `${deleteScanFileUrl}?id=${row.id}`,
            "renderScanDocumentsInfo"
        )}
    `;
};

const openScanFileModal = (
    method = "post",
    url = storeScanFileUrl,
    data = {}
) => {
    createContractScanFileModalForm.setAttribute("action", url);

    const inMethod = createContractScanFileModalForm?.querySelector(
        'input[name="_method"]'
    );
    if (inMethod) inMethod.value = method;

    if (method == "patch") {
        selectValueMapping = {};
        inputValueFormatter = {};
        autoMatchFieldAndFillPatchForm(
            createContractScanFileModalForm,
            method,
            data
        );
    } else {
        createContractScanFileModalForm.reset();
        refreshSumoSelect($(createContractScanFileModalForm).find("select"));
    }

    showAndSetAcceptExtsScanDocument(data?.type || {});
    showModal(createContractScanFileModal);
};

const showAndSetAcceptExtsScanDocument = (record = {}) => {
    let joinExt = "";
    let extensions = [];

    if (record || Object.keys(record).length > 0) {
        contractScanFileLabel.textContent = "Chọn file";
        contractScanFileInput.type = "file";
        extensions =
            record.extensions?.map((item) => `.${item.extension?.extension}`) ||
            [];
        joinExt = extensions.join(",");
        contractScanFileInput.setAttribute("accept", joinExt);
    }

    let span = contractScanFileLabel.querySelector(
        "span.contract-scan-file-type-extensions"
    );
    if (!span) {
        span = getOrCreateFormattedSpan(
            contractScanFileInput,
            "contract-scan-file-type-extensions text-info ms-2"
        );
        contractScanFileLabel.appendChild(span);
    }

    span.textContent = joinExt;
};

contractScanFileType.addEventListener("change", () => {
    const option =
        contractScanFileType.options[contractScanFileType.selectedIndex];
    const record = JSON.parse(option?.getAttribute("data-record") || "{}");
    showAndSetAcceptExtsScanDocument(record);
});

createContractScanFileModalForm.addEventListener("submit", async (e) => {
    appendContractIdInForm(createContractScanFileModalForm);

    await handleSubmitForm(e, createContractScanFileModalForm, () => {
        showAndSetAcceptExtsScanDocument();
        renderScanDocumentsInfo();
    });
});

scanDocumentTypeFilter.addEventListener("change", () => {
    renderScanDocumentsInfo();
});
