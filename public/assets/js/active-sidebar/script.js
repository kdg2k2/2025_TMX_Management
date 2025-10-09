document.addEventListener("DOMContentLoaded", () => {
    const currentPath = window.location.pathname.replace(/\/+$/, ""); // bỏ dấu / cuối
    const links = document.querySelectorAll(".main-menu li a");

    links.forEach(link => {
        const href = link.getAttribute("href");
        if (!href || href.startsWith("javascript")) return; // bỏ qua link trống / javascript:void

        let linkPath = "";
        try {
            const url = new URL(href, window.location.origin);
            linkPath = url.pathname.replace(/\/+$/, "");
        } catch (err) {
            return;
        }

        // Kiểm tra nếu đường dẫn hiện tại trùng
        if (currentPath === linkPath) {
            link.classList.add("active");

            // Mở tất cả menu cha chứa link này
            let li = link.closest("li");
            while (li) {
                if (li.classList.contains("has-sub")) {
                    li.classList.add("open");

                    // Mở submenu nếu có
                    const submenu = li.querySelector(":scope > ul");
                    if (submenu) submenu.style.display = "block";

                    // Gắn active cho thẻ a cùng cấp với ul (thường là tiêu đề menu cha)
                    const parentAnchor = li.querySelector(":scope > a.side-menu__item");
                    if (parentAnchor) parentAnchor.classList.add("active");
                }
                li = li.parentElement?.closest("li");
            }
        }
    });
});
