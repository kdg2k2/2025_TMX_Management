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
                        ${row.path ? createViewBtn(row.path) : ""}
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
        "bidding_software_ownerships",
        deleteBySoftwareOwnershipIdBiddingSoftwareOwnershipUrl,
        () => loadListBiddingSoftwareOwnership()
    );
};

window.loadListBiddingSoftwareOwnership = (
    table = selectedSoftwareOwnership
) => {
    initSelectedTable(
        $(table),
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
                                ? createViewBtn(row?.software_ownership?.path)
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
                res?.data?.data?.map((item) => item?.software_ownership_id) ||
                []);
            findAndChecked(originalSoftwareOwnership, ids);
        }
    );
};

window.tabBiddingSoftwareOwnership = () => {
    loadListSoftwareOwnership();
    loadListBiddingSoftwareOwnership();
};
