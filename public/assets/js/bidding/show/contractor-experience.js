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
                                ? createViewBtn(row.path_file_full)
                                : ""
                        }
                        ${
                            row.path_file_short
                                ? createViewBtn(row.path_file_short)
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
        "contractorExperience",
        storeBiddingContractorExperienceUrl,
        "bidding_contractor_experiences",
        deleteByContractIdBiddingContractorExperienceUrl,
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
                                ? createViewBtn(row?.contract?.path_file_full)
                                : ""
                        }
                        ${
                            row?.contract?.path_file_short &&
                            row?.file_type?.original == "path_file_short"
                                ? createViewBtn(row?.contract?.path_file_short)
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

window.tabBiddingContractorExperience = () => {
    loadListContract();
    loadListBiddingContractorExperience();
};

document.addEventListener("DOMContentLoaded", () => {
    tabBiddingContractorExperience();
});
