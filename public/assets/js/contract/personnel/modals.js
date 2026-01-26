const personnelModal = document.getElementById("personnel-modal");
const importPersonnelModal = document.getElementById("import-personnel-modal");
const importPersonnelModalForm = importPersonnelModal.querySelector("form");
var currentBtnData = null;

const loadPersonnel = async (btn, params = {}) => {
    if (!btn) btn = currentBtnData;
    currentBtnData = btn;

    createDataTableServerSide(
        $(personnelModal.querySelector("table")),
        apiContractPersonnelList,
        [
            {
                data: null,
                title: "Họ và tên",
                render: (data, type, row) => {
                    return row?.personnel?.name || "";
                },
            },
            {
                data: null,
                title: "Có trong hợp đồng",
                render: (data, type, row) => {
                    return createBadge(
                        row?.is_in_contract?.converted,
                        row?.is_in_contract?.color,
                        row?.is_in_contract?.icon,
                    );
                },
            },
            {
                data: null,
                title: "Chức danh",
                render: (data, type, row) => {
                    return row?.position || "";
                },
            },
            {
                data: null,
                title: "Chức danh (EN)",
                render: (data, type, row) => {
                    return row?.position_en || "";
                },
            },
            {
                data: null,
                title: "Đơn vị huy động",
                render: (data, type, row) => {
                    return row?.mobilized_unit || "";
                },
            },
            {
                data: null,
                title: "Đơn vị huy động (EN)",
                render: (data, type, row) => {
                    return row?.mobilized_unit_en || "";
                },
            },
            {
                data: null,
                title: "Nhiệm vụ thực hiện trong hợp đồng",
                render: (data, type, row) => {
                    return row?.task || "";
                },
            },
            {
                data: null,
                title: "Nhiệm vụ thực hiện trong hợp đồng (EN)",
                render: (data, type, row) => {
                    return row?.task_en || "";
                },
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                title: "Hành động",
                className: "text-center",
                render: (data, type, row) => {
                    return "";
                },
            },
        ],
        (item) => item,
        {
            paginate: 1,
            contract_id: btn.dataset.contract_id,
            ...params,
        },
        async () => {
            personnelModal.querySelector(".action").innerHTML = `
                <div class="d-flex justify-content-center align-items-center">
                    <div class="mt-1 me-1">
                        <label>
                            Nhân sự
                        </label>
                        <select class="personnel-filter"></select>
                    </div>
                    <div class="mt-1">
                        <label>
                            Có trong hợp đồng
                        </label>
                        <select class="is-in-contract-filter"></select>
                    </div>
                </div>
                <div>
                    ${
                        createActionBtn(
                            "warning",
                            "Import Excel",
                            `${contractPersonnelImport}?contract_id=${btn.dataset.contract_id}`,
                            "loadPersonnel",
                            "showImportPersonnelModal",
                            "ti ti-upload",
                        ) +
                        createActionBtn(
                            "info",
                            "Export Excel",
                            `${contractPersonnelExport}?contract_id=${btn.dataset.contract_id}`,
                            "loadPersonnel",
                            "exportBtnClick",
                            "ti ti-download",
                        )
                    }
                </div>
            `;

            fillSelectElement(
                personnelModal.querySelector(".action select.personnel-filter"),
                $personnels,
                "id",
                "name",
                "[Chọn]",
                true,
                params?.personnel_id || "",
            );

            fillSelectElement(
                personnelModal.querySelector(
                    ".action select.is-in-contract-filter",
                ),
                Object.values($isInContract),
                "original",
                "converted",
                "[Chọn]",
                true,
                params?.is_in_contract || "",
            );

            $(personnelModal)
                .find(".action select")
                .on("change", function () {
                    loadPersonnel(currentBtnData, {
                        personnel_id: personnelModal.querySelector(
                            ".action select.personnel-filter",
                        ).value,
                        is_in_contract: personnelModal.querySelector(
                            ".action select.is-in-contract-filter",
                        ).value,
                    });
                });
        },
    );
};

const showPersonnelModal = (btn) => {
    openModalBase(btn, {
        modal: personnelModal,
        afterShow: () => {
            loadPersonnel(btn);
        },
    });
};

const showImportPersonnelModal = (btn) => {
    resetFormAfterSubmit(importPersonnelModalForm);
    openModalBase(btn, {
        modal: importPersonnelModal,
        form: importPersonnelModalForm,
    });
};

const exportBtnClick = async (btn) => {
    const res = await http.get(btn.dataset.href);
    if (res?.data) downloadFileHandler(res.data);
};

importPersonnelModalForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, () => {
        hideModal(importPersonnelModalForm.closest(".modal"));
        loadPersonnel();
    });
});
