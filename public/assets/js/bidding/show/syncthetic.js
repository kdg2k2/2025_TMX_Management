const synctheticContractorExperience = document.getElementById(
    "syncthetic-contractor-experience"
);
const synctheticEligibility = document.getElementById("syncthetic-eligibility");
const synctheticSoftwareOwnership = document.getElementById(
    "syncthetic-software-ownership"
);
const synctheticImplementationPersonnel = document.getElementById(
    "syncthetic-implementation-personnel"
);
const synctheticProofContract = document.getElementById(
    "syncthetic-proof-contract"
);
const synctheticOrtherFile = document.getElementById("syncthetic-orther-file");

const synctheticDownloadBtn = document.getElementById(
    "syncthetic-download-btn"
);

synctheticDownloadBtn.addEventListener("click", async () => {
    const res = await http.get(biddingDownloadBuiltResultUrl, {
        id: $data["id"],
    });
    if (res.data) downloadFileHandler(res.data);
});

window.synctheticTab = () => {
    loadListBiddingContractorExperience(synctheticContractorExperience);
    loadListBiddingEligibility(synctheticEligibility);
    loadListBiddingSoftwareOwnership(synctheticSoftwareOwnership);
    loadTableImplementationPersonnel(synctheticImplementationPersonnel);
    loadListBiddingProofContract(synctheticProofContract);
    loadTableOrtherFile(synctheticOrtherFile);
};
