const renderPagination = (pagination) => {
    const paginationEl = document.getElementById("pagination");

    if (pagination.last_page <= 1) {
        paginationEl.innerHTML = "";
        return;
    }

    paginationEl.innerHTML = "";
    const currentPage = pagination.current_page;
    const lastPage = pagination.last_page;

    const prevDisabled = currentPage === 1;
    paginationEl.innerHTML += `
        <li class="page-item ${prevDisabled ? "disabled" : ""}">
            <a class="page-link" href="javascript:void(0);"
                ${
                    !prevDisabled
                        ? `onclick="changePage(${currentPage - 1})"`
                        : ""
                }>
                <i class="ti ti-chevron-left"></i>
            </a>
        </li>
    `;

    if (currentPage > 2) {
        paginationEl.innerHTML += `
            <li class="page-item">
                <a class="page-link" href="javascript:void(0);" onclick="changePage(1)">1</a>
            </li>
        `;
        if (currentPage > 3) {
            paginationEl.innerHTML += `
                <li class="page-item disabled">
                    <a class="page-link" href="javascript:void(0);">...</a>
                </li>
            `;
        }
    }

    for (
        let i = Math.max(1, currentPage - 1);
        i <= Math.min(lastPage, currentPage + 1);
        i++
    ) {
        paginationEl.innerHTML += `
            <li class="page-item ${i === currentPage ? "active" : ""}">
                <a class="page-link" href="javascript:void(0);" onclick="changePage(${i})">${i}</a>
            </li>
        `;
    }

    if (currentPage < lastPage - 1) {
        if (currentPage < lastPage - 2) {
            paginationEl.innerHTML += `
                <li class="page-item disabled">
                    <a class="page-link" href="javascript:void(0);">...</a>
                </li>
            `;
        }
        paginationEl.innerHTML += `
            <li class="page-item">
                <a class="page-link" href="javascript:void(0);" onclick="changePage(${lastPage})">${lastPage}</a>
            </li>
        `;
    }

    const nextDisabled = currentPage === lastPage;
    paginationEl.innerHTML += `
        <li class="page-item ${nextDisabled ? "disabled" : ""}">
            <a class="page-link" href="javascript:void(0);"
                ${
                    !nextDisabled
                        ? `onclick="changePage(${currentPage + 1})"`
                        : ""
                }>
                <i class="ti ti-chevron-right"></i>
            </a>
        </li>
    `;
};

const changePage = (page) => {
    customDataTableFilterParams.page = page;
    loadList();
    window.scrollTo({ top: 0, behavior: "smooth" });
};
