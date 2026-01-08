const ortherFileForm = document.getElementById("orther-file-form");
const ortherFileCloneRow = document.getElementById("orther-file-clone-row");
const btnAddRowOrtherFile = ortherFileCloneRow.querySelector("button");
const tableOrtherFile = document.getElementById("table-orther-file");

window.loadTableOrtherFile = (table = tableOrtherFile) => {
    createDataTableServerSide(
        $(table),
        listBiddingOrtherFileUrl,
        [
            {
                data: "content",
                title: "Nội dung",
            },
            createCreatedByAtColumn(),
            createCreatedUpdatedColumn(),
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
    cloneRow(ortherFileCloneRow, ortherFileForm);
});

ortherFileForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, () => {
        resetFormRows(ortherFileForm, ortherFileCloneRow);
        loadTableOrtherFile();
    });
});
