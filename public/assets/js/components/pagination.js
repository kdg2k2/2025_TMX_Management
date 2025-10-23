const createPagination = (data, type, onPageChange, paginationId = null) => {
    const id =
        paginationId ||
        (type === "original" ? "pagination-original" : "pagination-selected");
    const pagination = document.getElementById(id);

    if (!pagination) return;

    pagination.innerHTML = "";

    if (data.last_page <= 1) return;

    // Previous button
    const prevLi = document.createElement("li");
    prevLi.className = `page-item ${data.current_page === 1 ? "disabled" : ""}`;
    const prevLink = document.createElement("a");
    prevLink.className = "page-link";
    prevLink.href = "javascript:void(0);";
    prevLink.innerHTML = '<i class="ri-arrow-left-s-line align-middle"></i>';
    if (data.current_page > 1) {
        prevLink.onclick = () => onPageChange(data.current_page - 1);
    }
    prevLi.appendChild(prevLink);
    pagination.appendChild(prevLi);

    // Page numbers
    for (let i = 1; i <= data.last_page; i++) {
        if (
            i === 1 ||
            i === data.last_page ||
            (i >= data.current_page - 1 && i <= data.current_page + 1)
        ) {
            const pageLi = document.createElement("li");
            pageLi.className = `page-item ${
                i === data.current_page ? "active" : ""
            }`;
            const pageLink = document.createElement("a");
            pageLink.className = "page-link";
            pageLink.href = "javascript:void(0);";
            pageLink.textContent = i;
            pageLink.onclick = () => onPageChange(i);
            pageLi.appendChild(pageLink);
            pagination.appendChild(pageLi);
        } else if (i === data.current_page - 2 || i === data.current_page + 2) {
            const dotsLi = document.createElement("li");
            dotsLi.className = "page-item";
            dotsLi.innerHTML =
                '<a class="page-link" href="javascript:void(0);"><i class="bi bi-three-dots"></i></a>';
            pagination.appendChild(dotsLi);
        }
    }

    // Next button
    const nextLi = document.createElement("li");
    nextLi.className = `page-item ${
        data.current_page === data.last_page ? "disabled" : ""
    }`;
    const nextLink = document.createElement("a");
    nextLink.className = "page-link";
    nextLink.href = "javascript:void(0);";
    nextLink.innerHTML = '<i class="ri-arrow-right-s-line align-middle"></i>';
    if (data.current_page < data.last_page) {
        nextLink.onclick = () => onPageChange(data.current_page + 1);
    }
    nextLi.appendChild(nextLink);
    pagination.appendChild(nextLi);
};
