document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("submit-form");
    if (!form) return;

    const action = form.getAttribute("action")?.toLowerCase();
    const method =
        form.querySelector("input[name='_method']")?.value?.toLowerCase() ||
        "get";

    console.log({ form, method, action, $data });

    // Tự động fill form nếu có dữ liệu và là update (PATCH)
    if (method === "patch" && $data && typeof $data === "object") {
        Object.entries($data).forEach(([key, value]) => {
            const field = form.querySelector(`[name='${key}']`);
            if (!field) return;

            const tag = field.tagName.toLowerCase();

            if (tag === "input") {
                const type =
                    field.getAttribute("type")?.toLowerCase() || "text";

                if (["checkbox", "radio"].includes(type)) {
                    // Xử lý checkbox / radio
                    if (Array.isArray(value)) {
                        field.checked = value.includes(field.value);
                    } else {
                        field.checked = field.value == value;
                    }
                } else {
                    field.value = value ?? "";
                }
            } else if (tag === "select") {
                // Xử lý select
                const options = field.options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value == value) {
                        options[i].selected = true;
                        break;
                    }
                }
            } else if (tag === "textarea") {
                // Xử lý textarea
                field.value = value ?? "";
            }

            console.log(`Filled ${key} = ${value}`);
        });
    }

    // Submit form
    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const res = await http[method](action, formData);
        if (res.message && method === "post") form.reset();

        if (typeof afterSubmitDone == "function") afterSubmitDone();
    });
});
