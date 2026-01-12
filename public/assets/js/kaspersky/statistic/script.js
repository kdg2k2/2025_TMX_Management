const statsContainer = document.getElementById("stats-container");
const iframeSyntheticExcel = document.getElementById("iframe-synthetic-excel");
var currentExcelUrl = null;

const loadData = async () => {
    const year =
        document.getElementById("filter-year")?.value ||
        new Date().getFullYear();
    const month = document.getElementById("filter-month")?.value || "";

    const params = { year };
    if (month) params.month = month;

    const res = await http.get(apiKasperskyStatisticData, params);

    if (res?.data) {
        if (res?.data?.counter)
            renderStatsCards(
                res?.data?.counter,
                statsContainer,
                "col-sm-6 col-lg-4"
            );
        if (res?.data?.excel) {
            currentExcelUrl = res?.data?.excel;
            iframeSyntheticExcel.setAttribute(
                "src",
                createLinkPreviewFileOnline(currentExcelUrl)
            );
        }
    }
};

const resetFilter = () => {
    document.getElementById("filter-year").value = new Date().getFullYear();
    document.getElementById("filter-month").value = "";
    loadData();
};

document.addEventListener("DOMContentLoaded", async () => {
    await loadData();

    document
        .getElementById("btn-filter")
        .addEventListener("click", async () => {
            await loadData();
        });
    document.getElementById("btn-reset")?.addEventListener("click", () => {
        resetFilter();
        refreshSumoSelect();
    });
    document.getElementById("btn-download")?.addEventListener("click", () => {
        downloadFileHandler(currentExcelUrl);
    });
});
