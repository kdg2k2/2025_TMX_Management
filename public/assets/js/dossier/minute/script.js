const acceptModal = document.getElementById("accept-modal");
const acceptModalForm = acceptModal.querySelector("form");
const denyModal = document.getElementById("deny-modal");
const denyModalForm = denyModal.querySelector("form");

const getContractData = (row) => {
    switch (row.type.original) {
        case "plan":
            return row?.plan?.contract;
        case "handover":
            return row?.handover?.plan?.contract;
        case "usage_register":
            return row?.usage_register?.plan?.contract;
        default:
            return null;
    }
};

const renderColumns = () => {
    return [
        {
            data: null,
            title: "Năm",
            render: (data, type, row) => {
                return getContractData(row)?.year || "";
            },
        },
        {
            data: null,
            title: "Hợp đồng",
            render: (data, type, row) => {
                return getContractData(row)?.name || "";
            },
        },
        {
            data: null,
            title: "Số HĐ",
            render: (data, type, row) => {
                return getContractData(row)?.contract_number || "";
            },
        },
        {
            data: null,
            className: "text-center",
            title: "Loại biên bản",
            render: (data, type, row) => {
                return `<span class="p-1 text-white badge bg-${row.type.color}">${row.type.converted}</span>`;
            },
        },
        {
            data: null,
            className: "text-center",
            title: "Trạng thái",
            render: (data, type, row) => {
                return `<span class="p-1 text-white badge bg-${row.status.color}">${row.status.converted}</span>`;
            },
        },
        {
            data: null,
            orderable: false,
            searchable: false,
            className: "text-center",
            title: "Hành động",
            render: (data, type, row) => {
                return `
                    ${createViewBtn(row.path)}
                    ${
                        row.status.original == "pending_approval"
                            ? createBtn(
                                  "primary",
                                  "Phê duyệt",
                                  false,
                                  {
                                      "data-href": `${acceptUrl}?id=${row.id}`,
                                      "data-onsuccess": "loadList",
                                  },
                                  "ti ti-check",
                                  "openAcceptModal(this)"
                              )?.outerHTML +
                              createBtn(
                                  "danger",
                                  "Từ chối",
                                  false,
                                  {
                                      "data-href": `${denyUrl}?id=${row.id}`,
                                      "data-onsuccess": "loadList",
                                  },
                                  "ti ti-x",
                                  "openDenyModal(this)"
                              )?.outerHTML
                            : ""
                    }`;
            },
        },
    ];
};

const openAcceptModal = (btn) => {
    openModalBase(btn, {
        modal: acceptModal,
        form: acceptModalForm,
    });
};

const openDenyModal = (btn) => {
    openModalBase(btn, {
        modal: denyModal,
        form: denyModalForm,
    });
};

const bindSubmit = (form, modal) =>
    form.addEventListener("submit", (e) =>
        handleSubmitForm(e, form, () => hideModal(modal))
    );

bindSubmit(acceptModalForm, acceptModal);
bindSubmit(denyModalForm, denyModal);
