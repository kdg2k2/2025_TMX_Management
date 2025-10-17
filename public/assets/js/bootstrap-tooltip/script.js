var $tooltips =
    '[data-bs-toggle="tooltip"], [data-toggle="tooltip"], button[title], [data-bs-original-title], button[aria-label]';
var $customTooltips =
    "calcite-button[title], calcite-button[data-bs-original-title], calcite-button[aria-label], calcite-action[title], calcite-action[data-bs-original-title], calcite-action[aria-label]";
var tooltipObserver = null; // Lưu observer để tránh tạo nhiều lần

const initializeTooltips = () => {
    const tooltipSelector = [$tooltips, $customTooltips].join(", ");
    const queryScope = document.fullscreenElement || document;

    // Hàm init tooltips
    const doInit = () => {
        const tooltipElements = queryScope.querySelectorAll(tooltipSelector);

        tooltipElements.forEach((el) => {
            destroyTooltipForElement(el);
            createTooltipForElement(el);
        });
    };

    // Init ngay lập tức
    doInit();

    // Nếu fullscreen, query lại sau 500ms và 1000ms để bắt elements render muộn
    if (document.fullscreenElement) {
        setTimeout(doInit, 500);
        setTimeout(doInit, 1000);
    }

    // Setup observer
    if (!tooltipObserver) {
        setupTooltipObserver(tooltipSelector);
    }
};

const setupTooltipObserver = (tooltipSelector) => {
    tooltipObserver = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === "childList") {
                // Xử lý nodes được thêm vào
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === 1) {
                        if (node.matches && node.matches(tooltipSelector)) {
                            createTooltipForElement(node);
                        }

                        const tooltipElements =
                            node.querySelectorAll &&
                            node.querySelectorAll(tooltipSelector);
                        if (tooltipElements) {
                            tooltipElements.forEach((tooltipEl) => {
                                createTooltipForElement(tooltipEl);
                            });
                        }
                    }
                });

                mutation.removedNodes.forEach((node) => {
                    if (node.nodeType === 1) {
                        // Destroy tooltip của chính node bị xóa
                        if (node.matches && node.matches(tooltipSelector)) {
                            destroyTooltipForElement(node);
                        }

                        // Destroy tooltip của các child elements
                        const tooltipElements =
                            node.querySelectorAll &&
                            node.querySelectorAll(tooltipSelector);
                        if (tooltipElements) {
                            tooltipElements.forEach((tooltipEl) => {
                                destroyTooltipForElement(tooltipEl);
                            });
                        }
                    }
                });
            }
        });
    });

    // Observe container phù hợp
    const observeTarget = document.fullscreenElement || document.body;
    tooltipObserver.observe(observeTarget, {
        childList: true,
        subtree: true,
    });
};

const createTooltipForElement = (el) => {
    try {
        if (!document.body.contains(el)) return;

        destroyTooltipForElement(el);

        const isFullscreen = !!document.fullscreenElement;
        const container = isFullscreen ? document.fullscreenElement : "body";

        new bootstrap.Tooltip(el, {
            trigger: "hover",
            container: container,
            placement: "bottom",
        });
    } catch (error) {
        console.error("Failed to create tooltip:", error);
    }
};

const destroyTooltipForElement = (el) => {
    const existingTooltip = bootstrap.Tooltip.getInstance(el);
    if (existingTooltip) {
        existingTooltip.dispose();
    }
};

// Khi toggle fullscreen, cần disconnect observer cũ và tạo lại
const reinitializeTooltipsForFullscreen = () => {
    // Disconnect observer cũ
    if (tooltipObserver) {
        tooltipObserver.disconnect();
        tooltipObserver = null;
    }

    // Init lại tooltips và observer
    setTimeout(() => {
        initializeTooltips();
    }, 200);
};

document.addEventListener("DOMContentLoaded", () => {
    setTimeout(() => {
        initializeTooltips();
    }, 2000);
});
