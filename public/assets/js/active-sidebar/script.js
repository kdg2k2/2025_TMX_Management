document.addEventListener("DOMContentLoaded", () => {
    const currentPath = window.location.pathname.replace(/\/+$/, "");
    const links = document.querySelectorAll(".main-menu li a");

    links.forEach(link => {
        const href = link.getAttribute("href");
        if (!href || href.startsWith("javascript")) return;

        let linkPath = "";
        try {
            const url = new URL(href, window.location.origin);
            linkPath = url.pathname.replace(/\/+$/, "");
        } catch (err) {
            return;
        }

        // Lấy phần base (ví dụ: /contract/index → /contract/)
        const segments = linkPath.split('/').filter(Boolean);
        segments.pop(); // Bỏ phần cuối (index/create/edit)
        const basePattern = '/' + segments.join('/');

        // Check: exact match HOẶC current path starts with base pattern
        const isActive = (currentPath === linkPath) ||
                        (basePattern && currentPath.startsWith(basePattern + '/'));

        if (isActive) {
            link.classList.add("active");

            let li = link.closest("li");
            while (li) {
                if (li.classList.contains("has-sub")) {
                    li.classList.add("open");
                    const submenu = li.querySelector(":scope > ul");
                    if (submenu) submenu.style.display = "block";
                    const parentAnchor = li.querySelector(":scope > a.side-menu__item");
                    if (parentAnchor) parentAnchor.classList.add("active");
                }
                li = li.parentElement?.closest("li");
            }
        }
    });
});
