var customDataTableFilterParams = {
    paginate: 1,
    device_id: deviceId,
};

window.loadList = async () => {
    try {
        const res = await http.get(listUrl, customDataTableFilterParams);
        if (res?.data) {
            renderCards(res.data.data);
            renderPagination(res.data);
        }
    } catch (error) {
        console.error("Error loading list:", error);
    }
};

const renderCards = (data) => {
    const container = document.getElementById("cards-container");
    container.innerHTML = "";

    if (data.length == 0) {
        container.innerHTML =
            '<div class="text-center"><h3>Không có dữ liệu</h3></div>';
        return;
    }

    data.forEach((item) => {
        const card = `
            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                <div class="image-card-wrapper position-relative">
                    <a href="${
                        item.path
                    }" class="glightbox d-block" data-gallery="gallery1">
                        <img src="${
                            item.path
                        }" alt="image" class="img-fluid rounded img-thumbnail w-100">
                    </a>

                    <!-- Hover Actions -->
                    <div class="image-hover-actions">
                        ${createEditModalBtn(
                            `${updateUrl}?id=${item.id}`,
                            "loadList",
                            "openUpdateModal"
                        )}
                        ${createDeleteBtn(`${deleteUrl}?id=${item.id}`)}
                    </div>
                </div>
            </div>
        `;
        container.innerHTML += card;
    });

    initGLightbox();
};

document.addEventListener("DOMContentLoaded", () => {
    loadList();
});
