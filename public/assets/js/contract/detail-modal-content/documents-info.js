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

const renderDocumentsInfo = () => {
    createDataTableServerSide(
        $(documentsInfoDatatable),
        listFileUrl,
        renderDocumentsInfoColumns(),
        (item) => item,
        {
            paginate: 1,
        },
        (response) => {
            $("#document-count").text(response.data.total);
        }
    );
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
                return row?.created_by?.name;
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
            title: "Thời gian tạo/cập nhật",
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
                return renderDocumentsInfoActionButtons(row);
            },
        },
    ];
};

const viewFileHandler = async (id) => {
    const res = await http.post(`${viewFileUrl}?id=${id}`);
    if (res.data) window.open(`${res.data}`, "_blank");
};

const renderDocumentsInfoActionButtons = (row) => {
    return `
        ${createDeleteBtn(`${deleteFileUrl}?id=${row.id}`)}
        ${
            createBtn(
                "info",
                "Xem file",
                false,
                {},
                "ti ti-eye-search",
                `viewFileHandler(${row.id})`
            )?.outerHTML
        }
        ${
            createBtn(
                "success",
                "Tải file",
                false,
                {},
                "ti ti-download",
                `window.open('${row.path}', "_blank")`
            )?.outerHTML
        }
    `;
};

const openCreateFileModal = () => {
    const modal = new bootstrap.Modal(createContractFileModal, {
        backdrop: "static",
        keyboard: false,
    });
    modal.show();
};

const showAndSetAcceptExts = (extensions) => {
    var span = contractFileLabel.querySelector(
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

    const joinExt = `(${extensions.join(",")})`;
    span.innerText = joinExt;
    contractFileInput.setAttribute("accept", joinExt);
};

createContractFileModal.addEventListener("show.bs.modal", () => {
    contractFileType.addEventListener("change", () => {
        const option = contractFileType.options[contractFileType.selectedIndex];
        const record = JSON.parse(option?.getAttribute("data-record") || "");
        const extensions =
            record?.extensions?.map(
                (value, index) => `.${value?.extension.extension}`
            ) ?? [];
        showAndSetAcceptExts(extensions);
    });

    createContractFileModalForm.addEventListener("submit", async (e) => {
        await handleSubmitForm(e, createContractFileModalForm, () => {
            refreshSumoSelect();
            renderDocumentsInfo();
        });
    });
});
