document.addEventListener("DOMContentLoaded", () => {
    const currentPath = window.location.pathname.replace(/\/+$/, "");
    const links = document.querySelectorAll(".main-menu li a");
    const activeSuffixes = ["create", "edit", "show"];
    let activeLinkElement = null; // Biến để lưu trữ phần tử link đang active

    links.forEach((link) => {
        const href = link.getAttribute("href");
        if (!href || href.startsWith("javascript")) return;

        let linkPath = "";
        try {
            const url = new URL(href, window.location.origin);
            linkPath = url.pathname.replace(/\/+$/, "");
        } catch {
            return;
        }

        if (!linkPath || linkPath === "/") return;

        const currentSegments = currentPath.split("/").filter(Boolean);
        const linkSegments = linkPath.split("/").filter(Boolean);

        let isActive = currentPath === linkPath;

        // Logic so khớp tiền tố (prefix)
        if (!isActive && currentPath.startsWith(linkPath + "/")) {
            const prefixMatch = linkSegments.every(
                (seg, i) => seg === currentSegments[i]
            );
            if (prefixMatch) isActive = true;
        }

        // Logic cho các hành động 'index' (ví dụ: /users/create active link /users/index)
        if (!isActive) {
            const currentBase = currentSegments.slice(0, -1).join("/");
            const linkBase = linkSegments.slice(0, -1).join("/");
            const lastSegment = currentSegments[currentSegments.length - 1];

            if (
                linkSegments.at(-1) === "index" &&
                linkSegments.length > 0 && // Đảm bảo linkPath có ít nhất một segment
                currentBase === linkBase &&
                activeSuffixes.includes(lastSegment)
            ) {
                isActive = true;
            }
        }

        if (isActive) {
            link.classList.add("active");
            activeLinkElement = link; // Lưu trữ phần tử active

            // Mở các menu cha
            let li = link.closest("li");
            while (li) {
                if (li.classList.contains("has-sub")) {
                    li.classList.add("open");
                    const submenu = li.querySelector(":scope > ul");
                    if (submenu) submenu.style.display = "block";
                    const parentAnchor = li.querySelector(
                        ":scope > a.side-menu__item"
                    );
                    if (parentAnchor) parentAnchor.classList.add("active");
                }
                li = li.parentElement?.closest("li");
            }
        }
    });

    // Cuộn phần tử sidebar chứa menu để đưa mục menu active vào tầm nhìn.
    const scrollToActiveMenuItem = () => {
        if (!activeLinkElement) return;

        // Tìm phần tử chứa có thể cuộn (sidebar/menu container)
        // Thay đổi ".main-menu" bằng selector thực tế của sidebar chứa
        // nếu nó khác (ví dụ: "#sidebar-wrapper", ".app-sidebar")
        const sidebarContainer = document.querySelector(".main-menu")?.closest(".app-sidebar") || document.querySelector(".main-menu")?.parentElement;

        if (sidebarContainer) {
            activeLinkElement.scrollIntoView({
                behavior: "smooth", // Cho hiệu ứng cuộn mượt mà
                block: "center",    // Căn giữa mục menu active trong tầm nhìn
                inline: "nearest"
            });
        }
    };

    // Gọi hàm cuộn sau khi tất cả logic đánh dấu active đã chạy
    scrollToActiveMenuItem();
});
