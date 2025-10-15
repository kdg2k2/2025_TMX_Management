document.addEventListener("DOMContentLoaded", () => {
    const currentPath = window.location.pathname.replace(/\/+$/, "");
    const links = document.querySelectorAll(".main-menu li a");
    const activeSuffixes = ["create", "edit", "show"];

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

        if (!isActive && currentPath.startsWith(linkPath + "/")) {
            const prefixMatch = linkSegments.every(
                (seg, i) => seg === currentSegments[i]
            );
            if (prefixMatch) isActive = true;
        }

        if (!isActive) {
            const currentBase = currentSegments.slice(0, -1).join("/");
            const linkBase = linkSegments.slice(0, -1).join("/");
            const lastSegment = currentSegments[currentSegments.length - 1];

            if (
                linkSegments.at(-1) === "index" &&
                currentBase === linkBase &&
                activeSuffixes.includes(lastSegment)
            ) {
                isActive = true;
            }
        }

        if (isActive) {
            link.classList.add("active");

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
});
