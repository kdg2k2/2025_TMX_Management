const originalSoftwareOwnership = document.getElementById(
    "original-software-ownership"
);
const selectedSoftwareOwnership = document.getElementById(
    "selected-software-ownership"
);

window.loadListSoftwareOwnership = () => {
    initOriginalTable(
        $(originalSoftwareOwnership),
        listSoftwareOwnershipsUrl,
        [
            {
                data: "name",
                title: "Tên văn bản",
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
                            row.path
                                ? createBtn(
                                      "info",
                                      "Xem file full",
                                      false,
                                      {},
                                      "ti ti-file-type-pdf",
                                      `viewFileHandler('${row.path}')`
                                  )?.outerHTML
                                : ""
                        }
                `;
                },
            },
        ],
        (res) => {
            handleOriginalTableChangePage(
                originalSoftwareOwnership,
                "softwareOwnership"
            );
        },
        "softwareOwnership",
        storeBiddingSoftwareOwnershipUrl,
        "bindding_software_ownerships",
        deleteBySoftwareOwnershipIdBiddingSoftwareOwnershipUrl,
        "loadListBiddingSoftwareOwnership"
    );
};

window.loadListBiddingSoftwareOwnership = () => {
    initSelectedTable(
        $(selectedSoftwareOwnership),
        listBiddingSoftwareOwnershipUrl,
        [
            {
                data: null,
                title: "Tên văn bản",
                render: (data, type, row) => {
                    return row?.software_ownership?.name || "";
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
                            row?.software_ownership?.path
                                ? createBtn(
                                      "info",
                                      "Xem file full",
                                      false,
                                      {},
                                      "ti ti-file-type-pdf",
                                      `viewFileHandler('${row?.software_ownership?.path}')`
                                  )?.outerHTML
                                : ""
                        }
                        ${createDeleteBtn(
                            `${deleteBiddingSoftwareOwnershipUrl}?id=${row.id}`,
                            "loadListBiddingSoftwareOwnership"
                        )}
                `;
                },
            },
        ],
        (res) => {
            const ids = (resultSummary["softwareOwnership"] =
                res?.data?.data?.map((item) => item?.software_ownership_id) || []);
            findAndChecked(originalSoftwareOwnership, ids);
        }
    );
};

window.tabBiddingSoftwareOwnership = () => {
    loadListSoftwareOwnership();
    loadListBiddingSoftwareOwnership();
};
