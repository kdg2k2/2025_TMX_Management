const renderStatsCards = (
    counter,
    container,
    classSize = "col-md-6 col-xl-4"
) => {
    container.innerHTML = "";

    counter.forEach((element) => {
        const colDiv = document.createElement("div");
        colDiv.className = classSize;

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

        container.appendChild(colDiv);
    });
};
