const setRequiredStyle = (
    elements = document.querySelectorAll("input, select, textarea")
) => {
    elements.forEach((el) => {
        const $el = $(el);

        // Chỉ xử lý nếu có required
        if (!$el.attr("required")) {
            // Xóa dấu * nếu không còn required
            let $label;
            if ($el.is("select.SumoUnder")) {
                $label = $el.parent().siblings("label");
            } else {
                $label = $el.siblings("label");
                if ($label.length === 0) {
                    $label = $el.closest(".form-group").find("label");
                }
            }
            if ($label.length > 0) {
                $label.find(".required-star").remove();
            }
            return;
        }

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
                $label = $el.closest(".form-group").find("label");
            }
        }

        // Nếu không có label hoặc label nằm trong SelectBox => bỏ qua
        if ($label.length === 0 || $label.closest(".SelectBox").length > 0)
            return;

        // Nếu label chưa có dấu *
        if (!$label.html().includes("required-star"))
            $label.append('<span class="required-star text-danger">*</span>');
    });
};

const initRequiredFieldsStyle = () => {
    // Xử lý cho phần tử hiện tại
    setRequiredStyle();

    // Theo dõi DOM thay đổi (modal, ajax load, dynamic render,...)
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            // Xử lý khi có thuộc tính thay đổi
            if (
                mutation.type === "attributes" &&
                mutation.attributeName === "required"
            ) {
                setRequiredStyle([mutation.target]);
            }

            // Xử lý khi có node mới
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1) {
                    if (node.matches("input, select, textarea")) {
                        setRequiredStyle([node]);
                    }
                    const fields = node.querySelectorAll(
                        "input, select, textarea"
                    );
                    if (fields.length) setRequiredStyle(fields);
                }
            });
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ["required"],
    });
};

document.addEventListener("DOMContentLoaded", () => {
    setTimeout(() => initRequiredFieldsStyle(), 100);
});
