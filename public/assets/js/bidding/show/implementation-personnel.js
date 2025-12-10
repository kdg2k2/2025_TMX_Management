const implementationPersonnelForm = document.getElementById(
    "implementation-personnel-form"
);
const implementationPersonnelCloneRow = document.getElementById(
    "implementation-personnel-clone-row"
);
const btnAddRowImplementationPersonnel =
    implementationPersonnelCloneRow.querySelector("button");

const tableImplementationPersonnel = document.getElementById(
    "table-implementation-personnel"
);

const loadAndFillSelectPersonnelUnits = async () => {
    const res = await http.get(listPersonnelUnitsUrl, {}, null, false);
    if (res.data) {
        fillSelectElement(
            implementationPersonnelCloneRow.querySelector(
                ".implementation-personnel-unit"
            ),
            res.data,
            "id",
            ["short_name", "name"]
        );

        fillSelectPersonels(
            implementationPersonnelCloneRow.querySelector(
                ".implementation-personnel"
            )
        );

        fillSelectPersonelFiles(
            implementationPersonnelCloneRow.querySelector(
                ".implementation-personnel-file"
            )
        );
    }
};

const fillSelectPersonels = (select, data = []) => {
    fillSelectElement(select, data, "id", ["name", "educational_level"]);
};

const fillSelectPersonelFiles = (select, data = []) => {
    fillSelectElement(
        select,
        data,
        "id",
        ["type.name", "updated_at"],
        "",
        false
    );
};

const loadAndFillSelectPersonels = async (value, select) => {
    const res = await http.get(
        listPersonnelsUrl,
        {
            personnel_unit_id: value || "",
        },
        null,
        false
    );
    if (res.data) fillSelectPersonels(select, res.data);
};

const loadAndFillSelectPersonelFiles = async (value, select) => {
    const res = await http.get(
        listPersonnelFilesUrl,
        {
            personnel_id: value | "",
        },
        null,
        false
    );
    if (res.data) fillSelectPersonelFiles(select, res.data);
};

btnAddRowImplementationPersonnel.addEventListener("click", () => {
    cloneRow(implementationPersonnelCloneRow, implementationPersonnelForm);
});

implementationPersonnelForm.addEventListener("change", (e) => {
    const target = e.target;
    const closestRow = target.closest(".clone-row");

    if (target.classList.contains("implementation-personnel-unit"))
        loadAndFillSelectPersonels(
            target.value,
            closestRow.querySelector("select.implementation-personnel")
        );
    if (target.classList.contains("implementation-personnel"))
        loadAndFillSelectPersonelFiles(
            target.value,
            closestRow.querySelector("select.implementation-personnel-file")
        );
});

window.loadTableImplementationPersonnel = (
    table = tableImplementationPersonnel
) => {
    createDataTableServerSide(
        $(table),
        listBiddingImplementationPersonnel,
        [
            {
                data: null,
                title: "Đơn vị",
                render: (data, type, row) => {
                    return row?.personnel?.personnel_unit?.name || "";
                },
            },
            {
                data: null,
                title: "Nhân sự",
                render: (data, type, row) => {
                    return row?.personnel?.name || "";
                },
            },
            {
                data: null,
                title: "Bằng cấp",
                render: (data, type, row) => {
                    return `<ul class="m-0">${row?.files
                        ?.map(
                            (value, index) =>
                                `<li role="button" class="link-primary" onclick="viewFileHandler(
                                        '${value?.personel_file?.path}'
                                    )">
                                    ${value?.personel_file?.type?.name}
                                        - ${formatDateTime(
                                            value?.personel_file?.type
                                                ?.updated_at
                                        )}
                                </li>`
                        )
                        .join("")}</ul>`;
                },
            },
            {
                data: null,
                title: "Chức danh",
                render: (data, type, row) => {
                    return row?.job_title?.converted || "";
                },
            },
            {
                data: null,
                title: "Người tạo - Cập nhật",
                render: (data, type, row) => {
                    return row?.created_by?.name || "";
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
                    return `
                    ${createDeleteBtn(
                        `${deleteBiddingImplementationPersonnel}?id=${row.id}`,
                        "loadTableImplementationPersonnel"
                    )}
                `;
                },
            },
        ],
        (item) => item,
        {
            paginate: 1,
        }
    );
};

implementationPersonnelForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, () => {
        resetFormRows(
            implementationPersonnelForm,
            implementationPersonnelCloneRow
        );
        loadTableImplementationPersonnel();
    });
});

window.tabImplementationPersonnel = async () => {
    resetFormRows(implementationPersonnelForm, implementationPersonnelCloneRow);
    await loadAndFillSelectPersonnelUnits();
    loadTableImplementationPersonnel();
};
