const modalRun = document.getElementById("modal-run");
const modalRunForm = modalRun.querySelector("form");

const renderColumns = () => {
    return [
        {
            data: "name",
            title: "Tên tiến trình",
        },
        {
            data: "description",
            title: "Mô tả",
        },
        {
            data: null,
            title: "Danh sách người nhận",
            render: (data, type, row) => {
                return `
                    <ul class="m-0">${row?.users
                        ?.map(
                            (i) => `
                        <li>${i?.name}</li>
                    `
                        )
                        .join("")}</ul>
                `;
            },
        },
        {
            data: null,
            title: "Trạng thái tiến trình",
            render: (data, type, row) => {
                if (row?.is_active)
                    return createBadge("Bật", "success", "ti ti-check");
                return createBadge("Tắt", "primary", "ti ti-x");
            },
        },
        {
            data: null,
            title: "Thời gian chạy cuối/tiếp theo",
            render: (data, type, row) => {
                return `
                    <ul class="m-0">
                        <li>${row.last_run_at || "Không xác định"}</li>
                        <li>${row.next_run_at || "Không xác định"}</li>
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
                return `
                ${
                    row?.is_active
                        ? createBtn(
                              "secondary",
                              "Chạy task thủ công",
                              false,
                              {
                                  "data-href": `${runUrl}?id=${row.id}`,
                                  "data-onsuccess": loadList,
                              },
                              "ti ti-clock-code",
                              "openRunModal(this)"
                          ).outerHTML
                        : ""
                }
                    ${createEditBtn(`${editUrl}?id=${row.id}`)}
                `;
            },
        },
    ];
};

const openRunModal = (btn) => {
    openModalBase(btn, {
        modal: modalRun,
        form: modalRunForm,
    });
};

modalRunForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, () => {
        hideModal(modalRun);
    });
});
