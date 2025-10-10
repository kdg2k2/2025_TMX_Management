const initRequiredFieldsStyle = () => {
    const setRequiredStyle = (elements) => {
        elements.forEach((el) => {
            const $el = $(el);

            // Chỉ xử lý nếu có required
            if (!$el.attr("required")) return;

            // Nếu nằm trong SelectBox => bỏ qua
            if ($el.closest(".SelectBox").length > 0) return;

            let $label;

            // Nếu là select.SumoUnder => label là phần tử cùng cấp cha
            if ($el.is("select.SumoUnder")) {
                $label = $el.parent().siblings("label");
            } else {
                // Tìm label thông thường
                $label = $el.siblings("label");
                if ($label.length === 0) {
                    $label = $el.parent().find("label");
                }
            }

            // Nếu không có label hoặc label nằm trong SelectBox => bỏ qua
            if ($label.length === 0 || $label.closest(".SelectBox").length > 0) return;

            // Nếu label chưa có dấu *
            if (!$label.html().includes("required-star")) {
                const labelText = $label.text().trim() + ' <span class="required-star text-danger">*</span>';
                $label.html(labelText);
            }
        });
    };

    // Xử lý cho phần tử hiện tại
    setRequiredStyle(document.querySelectorAll("input, select, textarea"));

    // Theo dõi DOM thay đổi (modal, ajax load, dynamic render,...)
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1) {
                    if (node.matches("input, select, textarea")) {
                        setRequiredStyle([node]);
                    }
                    const fields = node.querySelectorAll("input, select, textarea");
                    if (fields.length) setRequiredStyle(fields);
                }
            });
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });
};

document.addEventListener("DOMContentLoaded", () => {
    setTimeout(() => initRequiredFieldsStyle(), 100);
});
