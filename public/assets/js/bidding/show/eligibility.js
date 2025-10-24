const originalEligibility = document.getElementById("original-eligibility");
const selectedEligibility = document.getElementById("selected-eligibility");

window.loadListEligibility = () => {
    initOriginalTable(
        $(originalEligibility),
        listEligibilityUrl,
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
                originalEligibility,
                "eligibility"
            );
        },
        "eligibility",
        storeBiddingEligibilityUrl,
        "bidding_eligibility",
        deleteByEligibilityIdBiddingEligibilityUrl,
        "loadListBiddingEligibility"
    );
};

window.loadListBiddingEligibility = () => {
    initSelectedTable(
        $(selectedEligibility),
        listBiddingEligibilityUrl,
        [
            {
                data: null,
                title: "Tên văn bản",
                render: (data, type, row) => {
                    return row?.eligibility?.name || "";
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
                            row?.eligibility?.path
                                ? createBtn(
                                      "info",
                                      "Xem file full",
                                      false,
                                      {},
                                      "ti ti-file-type-pdf",
                                      `viewFileHandler('${row?.eligibility?.path}')`
                                  )?.outerHTML
                                : ""
                        }
                        ${createDeleteBtn(
                            `${deleteBiddingEligibilityUrl}?id=${row.id}`,
                            "loadListBiddingEligibility"
                        )}
                `;
                },
            },
        ],
        (res) => {
            const ids = (resultSummary["eligibility"] =
                res?.data?.data?.map((item) => item?.eligibility_id) || []);
            findAndChecked(originalEligibility, ids);
        }
    );
};

window.tabBiddingEligibility = () => {
    loadListEligibility();
    loadListBiddingEligibility();
};


document.addEventListener("DOMContentLoaded", () => {
    tabBiddingEligibility();
});
