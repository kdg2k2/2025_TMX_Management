const productModal = document.getElementById("product-modal");
const productTable = $(productModal.querySelector("table"));

const importProductModal = document.getElementById("import-product-modal");
const importProductModalForm = importProductModal.querySelector("form");

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
const responseInspectionProductModalForm = responseInspectionProductModal.querySelector("form");

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
        });
    });
});
