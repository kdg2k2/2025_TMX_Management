const ortherFileForm = document.getElementById("orther-file-form");
const ortherFileCloneRow = document.getElementById("orther-file-clone-row");
const btnAddRowOrtherFile = ortherFileCloneRow.querySelector("button");
const tableOrtherFile = document.getElementById("table-orther-file");

const getOrtherFileSelects = (jquery = false) => {
    return getFormInputsAndSelects(ortherFileForm, jquery);
};

const reindexOrtherFileRow = () => {
    reindexRow(ortherFileForm);
};

const getMaxOrtherFileIndex = () => {
    const selects = getOrtherFileSelects();
    return getMaxRowIndex(selects);
};

const renderRowOrtherFile = () => {
    return cloneRow(
        ortherFileCloneRow,
        ortherFileForm,
        () => reindexOrtherFileRow(),
        () => getOrtherFileSelects(true),
        () => getMaxOrtherFileIndex()
    );
};

window.loadTableOrtherFile = (table = tableOrtherFile) => {
    createDataTableServerSide(
        $(table),
        listBiddingOrtherFileUrl,
        [
            {
                data: "content",
                title: "Nội dung",
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
                    ${
                        createViewBtn(row.path) +
                        createDeleteBtn(
                            `${deleteBiddingOrtherFileUrl}?id=${row.id}`,
                            "loadTableOrtherFile"
                        )
                    }
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

btnAddRowOrtherFile.addEventListener("click", () => {
    renderRowOrtherFile();
});

ortherFileForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, ortherFileForm, () => {
        resetFormRows(ortherFileForm, ortherFileCloneRow);
        loadTableOrtherFile();
    });
});
