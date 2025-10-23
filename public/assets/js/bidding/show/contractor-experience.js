const originalContractorExperience = document.getElementById(
    "original-contractor-experience"
);
const selectedContractorExperience = document.getElementById(
    "selected-contractor-experience"
);

window.loadListContract = () => {
    initOriginalTable(
        $(originalContractorExperience),
        listContractUrl,
        [
            {
                data: "year",
                title: "Năm",
            },
            {
                data: "name",
                title: "Tên HĐ",
            },
            {
                data: null,
                title: "Chủ đầu tư",
                render: (data, type, row) => {
                    return [
                        row?.investor?.name_vi || "",
                        row?.investor?.name_en || "",
                    ]
                        .filter((v) => v != null && v !== "")
                        .join(" - ");
                },
            },
            {
                data: "signed_date",
                title: "Ngày ký",
            },
            {
                data: null,
                title: "Giá trị HĐ",
                render: (data, type, row) => {
                    return row?.contract_value
                        ? fmNumber(row?.contract_value) + " vnđ"
                        : "";
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
                            row.path_file_full
                                ? createBtn(
                                      "info",
                                      "Xem file full",
                                      false,
                                      {},
                                      "ti ti-file-type-pdf",
                                      `viewFileHandler('${row.path_file_full}')`
                                  )?.outerHTML
                                : ""
                        }
                        ${
                            row.path_file_short
                                ? createBtn(
                                      "info",
                                      "Xem file short",
                                      false,
                                      {},
                                      "ti ti-file-type-pdf",
                                      `viewFileHandler('${row.path_file_short}')`
                                  )?.outerHTML
                                : ""
                        }
                `;
                },
            },
        ],
        (res) => {
            handleOriginalTableChangePage(
                originalContractorExperience,
                "contractorExperience"
            );
        },
        resultSummary["contractorExperience"],
        storeBiddingContractorExperienceUrl,
        "bidding_contractor_experiences",
        deleteBiddingContractorExperienceUrl,
        "loadListBiddingContractorExperience"
    );
};

window.loadListBiddingContractorExperience = () => {
    initSelectedTable(
        $(selectedContractorExperience),
        listBiddingContractorExperienceUrl,
        [
            {
                data: null,
                title: "Năm",
                render: (data, type, row) => {
                    return row?.contract?.year || "";
                },
            },
            {
                data: null,
                title: "Tên HĐ",
                render: (data, type, row) => {
                    return row?.contract?.name || "";
                },
            },
            {
                data: null,
                title: "Bản HĐ",
                render: (data, type, row) => {
                    return row?.file_type?.converted || "";
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
                            row?.contract?.path_file_full &&
                            row?.file_type?.original == "path_file_full"
                                ? createBtn(
                                      "info",
                                      "Xem file full",
                                      false,
                                      {},
                                      "ti ti-file-type-pdf",
                                      `viewFileHandler('${row?.contract?.path_file_full}')`
                                  )?.outerHTML
                                : ""
                        }
                        ${
                            row?.contract?.path_file_short &&
                            row?.file_type?.original == "path_file_short"
                                ? createBtn(
                                      "info",
                                      "Xem file short",
                                      false,
                                      {},
                                      "ti ti-file-type-pdf",
                                      `viewFileHandler('${row?.contract?.path_file_short}')`
                                  )?.outerHTML
                                : ""
                        }
                        ${createDeleteBtn(
                            `${deleteBiddingContractorExperienceUrl}?id=${row.id}`,
                            "loadListBiddingContractorExperience"
                        )}
                `;
                },
            },
        ],
        (res) => {
            const ids = (resultSummary["contractorExperience"] =
                res?.data?.data?.map((item) => item?.contract_id) || []);
            findAndChecked(originalContractorExperience, ids);
        }
    );
};

document.addEventListener("DOMContentLoaded", () => {
    loadListContract();
    loadListBiddingContractorExperience();
});
