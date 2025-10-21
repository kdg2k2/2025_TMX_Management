const documentsInfoDatatable = document.getElementById(
    "documents-info-datatable"
);
const createContractFileModal = document.getElementById(
    "create-contract-file-modal"
);
const createContractFileModalForm =
    createContractFileModal.querySelector("form");
const contractFileType = document.getElementById("contract-file-type");
const contractFileLabel = document.getElementById("contract-file-label");
const contractFileInput = document.getElementById("contract-file-input");
const documentTypeFilter = document.getElementById("document-type-filter");

window.renderDocumentsInfo = () => {
    createDataTableServerSide(
        $(documentsInfoDatatable),
        listFileUrl,
        renderDocumentsInfoColumns(),
        (item) => item,
        getDocumentsInfoFilterParams(),
        (response) => {
            $("#document-count").text(response.data.total);
        }
    );
};

const getDocumentsInfoFilterParams = () => {
    const params = {
        paginate: 1,
        contract_id: contractId,
    };

    if (documentTypeFilter.value) params["type_id"] = documentTypeFilter.value;

    return params;
};

const renderDocumentsInfoColumns = () => {
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
            title: "Nội dung cập nhật",
            render: (data, type, row) => {
                return row?.updated_content;
            },
        },
        {
            data: null,
            title: "Ghi chú",
            render: (data, type, row) => {
                return row?.note;
            },
        },
        {
            data: null,
            title: "Người tạo",
            render: (data, type, row) => {
                return row?.created_by?.name;
            },
        },
        {
            data: null,
            title: "Thời gian tạo",
            render: (data, type, row) => {
                return row.created_at;
            },
        },
        {
            data: null,
            orderable: false,
            searchable: false,
            title: "Hành động",
            className: "text-center",
            render: (data, type, row) => {
                return renderDocumentsInfoActionButtons(row);
            },
        },
    ];
};

const renderDocumentsInfoActionButtons = (row) => {
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
            row?.type?.type == "file"
                ? createBtn(
                      "success",
                      "Tải",
                      false,
                      {},
                      "ti ti-download",
                      `downloadFileHandler('${row.path}')`
                  )?.outerHTML
                : ""
        }
        ${createDeleteBtn(
            `${deleteFileUrl}?id=${row.id}`,
            "renderDocumentsInfo"
        )}
    `;
};

const openCreateFileModal = () => {
    showModal(createContractFileModal);
};

const showAndSetAcceptExts = (record = {}) => {
    const contractFileInputParent = contractFileInput?.closest(".form-group");

    if (!record || Object.keys(record).length === 0) {
        contractFileInputParent.hidden = true;
        return;
    }

    contractFileInputParent.hidden = false;

    let joinExt = "";
    if (record.type === "file") {
        contractFileLabel.textContent = "Chọn file";
        contractFileInput.type = "file";
        const extensions =
            record.extensions?.map((item) => `.${item.extension?.extension}`) ||
            [];
        joinExt = extensions.join(",");
        contractFileInput.setAttribute("accept", joinExt);
    } else {
        contractFileLabel.textContent = "Nhập link";
        contractFileInput.type = "url";
        contractFileInput.removeAttribute("accept");
        joinExt = "Url";
    }

    let span = contractFileLabel.querySelector(
        "span.contract-file-type-extensions"
    );
    if (!span) {
        span = document.createElement("span");
        span.classList.add(
            "contract-file-type-extensions",
            "text-info",
            "ms-2"
        );
        contractFileLabel.appendChild(span);
    }

    span.textContent = joinExt;
};

contractFileType.addEventListener("change", () => {
    const option = contractFileType.options[contractFileType.selectedIndex];
    const record = JSON.parse(option?.getAttribute("data-record") || "{}");
    showAndSetAcceptExts(record);
});

createContractFileModalForm.addEventListener("submit", async (e) => {
    appendContractIdInForm(createContractFileModalForm);

    await handleSubmitForm(e, createContractFileModalForm, () => {
        showAndSetAcceptExts();
        renderDocumentsInfo();
    });
});

createContractFileModal.addEventListener("show.bs.modal", () => {
    showAndSetAcceptExts();
});

documentTypeFilter.addEventListener("change", () => {
    renderDocumentsInfo();
});
