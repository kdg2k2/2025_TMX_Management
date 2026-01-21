const productModal = document.getElementById("product-modal");
const productTable = $(productModal.querySelector("table"));

const importProductModal = document.getElementById("import-product-modal");
const importProductModalForm = importProductModal.querySelector("form");

const renderFilterYear = (id = null, setRequired = false) => {
    return `
        <label>Năm hợp đồng</label>
        <select id="${id || ""}" name="year" ${setRequired ? "required" : ""}></select>
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
            const filterContainerClass = "contract-year-filter-container";

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
importProductModalForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, () => {
        hideModal(importProductModal);
        productModal
            .querySelector(".contract-year-filter-container select")
            ?.dispatchEvent(new Event("change"));
    });
});
