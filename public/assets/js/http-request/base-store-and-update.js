// Tự động fill form nếu có dữ liệu và là update (PATCH)
const autoMatchFieldAndFillPatchForm = (form, method) => {
    if (method === "patch" && $data && typeof $data === "object") {
        Object.entries($data).forEach(([key, value]) => {
            const field = form.querySelector(
                `[name='${key}'], [name='${key}[]']`
            );

            if (!field) {
                return;
            }

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
                destroySumoSelect($(field));

                const matchKeys = ["id", "value", "code", "key"];
                // Chuẩn hoá dữ liệu trả về thành mảng giá trị
                if (Array.isArray(value)) {
                    // Trường hợp value là mảng object [{id: 1}, {id: 2}]
                    if (typeof value[0] === "object" && value[0] !== null) {
                        // Lấy key phù hợp (ưu tiên id, value, code, key)
                        const detectKey = matchKeys.find((k) => k in value[0]);
                        values = value.map((v) => String(v[detectKey]));
                    } else {
                        values = value.map(String);
                    }
                } else if (typeof value === "object" && value !== null) {
                    // Nếu là 1 object duy nhất {id: 3}
                    const detectKey = Object.keys(value).find((k) =>
                        matchKeys.includes(k)
                    );
                    values = [String(value[detectKey])];
                } else {
                    // Nếu là primitive (số, chuỗi, null)
                    values = [String(value ?? "")];
                }

                // Đánh dấu selected
                const options = field.options;
                for (let i = 0; i < options.length; i++) {
                    options[i].selected = values.includes(options[i].value);
                }

                initSumoSelect($(field));
            } else if (tag === "textarea") {
                // Xử lý textarea
                field.value = value ?? "";
            }
        });

        if (typeof afterAutoMatchFieldAndFillPatchFormDone == "function")
            afterAutoMatchFieldAndFillPatchFormDone();
    }
};

document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("submit-form");
    if (!form) return;

    const action = form.getAttribute("action")?.toLowerCase();
    const method =
        form.querySelector("input[name='_method']")?.value?.toLowerCase() ||
        "get";

    autoMatchFieldAndFillPatchForm(form, method);

    // Submit form
    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const res = await http[method](action, formData);
        if (res.message && method === "post") form.reset();

        if (typeof afterSubmitDone == "function") afterSubmitDone();
    });
});
