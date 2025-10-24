const originalProofContract = document.getElementById(
    "original-proof-contract"
);
const selectedProofContract = document.getElementById(
    "selected-proof-contract"
);

window.loadListProofContract = () => {
    initOriginalTable(
        $(originalProofContract),
        listProofContractsUrl,
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
                originalProofContract,
                "proofContract"
            );
        },
        "proofContract",
        storeBiddingProofContractUrl,
        "bidding_proof_contracts",
        deleteByProofContractIdBiddingProofContractUrl,
        "loadListBiddingProofContract"
    );
};

window.loadListBiddingProofContract = () => {
    initSelectedTable(
        $(selectedProofContract),
        listBiddingProofContractUrl,
        [
            {
                data: null,
                title: "Tên văn bản",
                render: (data, type, row) => {
                    return row?.proof_contract?.name || "";
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
                            row?.proof_contract?.path
                                ? createViewBtn(row?.proof_contract?.path)
                                : ""
                        }
                        ${createDeleteBtn(
                            `${deleteBiddingProofContractUrl}?id=${row.id}`,
                            "loadListBiddingProofContract"
                        )}
                `;
                },
            },
        ],
        (res) => {
            const ids = (resultSummary["proofContract"] =
                res?.data?.data?.map((item) => item?.proof_contract_id) || []);
            findAndChecked(originalProofContract, ids);
        }
    );
};

window.tabBiddingProofContract = () => {
    loadListProofContract();
    loadListBiddingProofContract();
};

document.addEventListener("DOMContentLoaded", () => {
    tabBiddingProofContract();
});
