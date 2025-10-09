window.loadList = () => {
    createDataTableServerSide(table, listUrl, renderColumns(), (item) => item, {
        paginate: 1,
    });
};

const createEditBtn = (url) => {
    return `
        <button class="mb-1 btn btn-sm btn-warning icon-btn b-r-4 edit-btn" onclick="window.location='${url}'" type="button" data-bs-placement="top" data-toggle="tooltip" data-bs-original-title="Cập nhật">
            <i class="ti ti-edit"></i>
        </button>
    `;
};

const createDeleteBtn = (url) => {
    return `
        <button class="mb-1 btn btn-sm btn-danger icon-btn b-r-4 delete-btn" type="button" data-bs-target="#modalDelete" data-bs-toggle="modal" data-href="${url}" data-onsuccess="loadList" data-bs-placement="top" data-toggle="tooltip" data-bs-original-title="Xóa">
            <i class="ti ti-trash"></i>
        </button>
    `;
};

document.addEventListener("DOMContentLoaded", () => {
    loadList();
});
