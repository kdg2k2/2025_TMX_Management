const renderCarousel = (carouselId, images) => {
    return `
        <div id="${carouselId}" class="carousel carousel-fade slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                ${images
                    .map(
                        (img, idx) => `
                    <div class="carousel-item ${idx === 0 ? "active" : ""}">
                         <div class="carousel-image-wrapper">
                            <a href="${
                                img?.path || img || getDefaultImage()
                            }" class="glightbox d-block" data-gallery="gallery1">
                                <img src="${
                                    img?.path || img
                                }" class="d-block w-100"
                                onerror="this.src='${getDefaultImage()}'">
                            </a>

                        </div>
                    </div>
                `
                    )
                    .join("")}
            </div>
            ${
                images.length > 1
                    ? `
                <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
                <div class="carousel-indicators">
                    ${images
                        .map(
                            (_, idx) => `
                        <button type="button" data-bs-target="#${carouselId}"
                            data-bs-slide-to="${idx}" ${
                                idx === 0 ? 'class="active"' : ""
                            }></button>
                    `
                        )
                        .join("")}
                </div>
            `
                    : ""
            }
        </div>`;
};
