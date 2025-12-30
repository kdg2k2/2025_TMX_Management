const statsContainer = document.getElementById("stats-container");

const loadData = async () => {
    const res = await http.get(apiDeviceStatisticData);
    if (res?.data?.counter) renderStats(res.data.counter);
};

const renderStats = (counter) => {
    statsContainer.innerHTML = "";

    counter.forEach((element) => {
        const colDiv = document.createElement("div");
        colDiv.className = "col-md-6 col-xl-4 mb-3";

        colDiv.innerHTML = `
            <div class="card custom-card dashboard-main-card overflow-hidden ${
                element?.color
            }">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-${
                                element?.color
                            } rounded">
                                <i class="${element?.icon} fs-2"></i>
                            </div>
                        </div>
                        <div class="flex-fill">
                            <span class="d-block text-muted mb-1">${
                                element?.converted
                            }</span>
                            <h3 class="fw-semibold mb-0">
                                ${fmNumber(element?.value)}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        `;

        statsContainer.appendChild(colDiv);
    });
};

document.addEventListener("DOMContentLoaded", async () => {
    await loadData();
});
