var selectValueMapping = (inputValueFormatter = {});

// Tự động fill form nếu có dữ liệu và là update (PATCH)
const autoMatchFieldAndFillPatchForm = (form, method) => {
    if (method === "patch" && $data && typeof $data === "object") {
        Object.entries($data).forEach(([key, value]) => {
            const field = form.querySelector(
                `[name='${key}'], [name='${key}[]']`
            );
            if (!field) return;

            const tag = field.tagName.toLowerCase();

            if (tag === "input") {
                const type =
                    field.getAttribute("type")?.toLowerCase() || "text";

                if (type == "file") return;

                if (["checkbox", "radio"].includes(type)) {
                    // Xử lý checkbox / radio
                    if (Array.isArray(value)) {
                        field.checked = value.includes(field.value);
                    } else {
                        field.checked = field.value == value;
                    }
                } else {
                    // Áp dụng formatter nếu có
                    const formatter = inputValueFormatter[key];
                    const formattedValue = formatter
                        ? formatter(value)
                        : value ?? "";
                    field.value = formattedValue;
                }
            } else if (tag === "select") {
                destroySumoSelect($(field));
                let values = [];

                const mapper =
                    selectValueMapping[field.name] || selectValueMapping[key];

                // Chuẩn hoá dữ liệu
                if (Array.isArray(value)) {
                    if (
                        value.length > 0 &&
                        typeof value[0] === "object" &&
                        value[0] !== null
                    ) {
                        if (mapper) {
                            values = value.map((v) => String(mapper(v)));
                        } else {
                            const matchKeys = ["id", "value", "code", "key"];
                            const detectKey = matchKeys.find(
                                (k) => k in value[0]
                            );
                            if (detectKey) {
                                values = value.map((v) => String(v[detectKey]));
                            }
                        }
                    } else if (value.length > 0) {
                        values = value.map(String);
                    }
                } else if (typeof value === "object" && value !== null) {
                    if (mapper) {
                        const mappedValue = mapper(value);
                        values = [String(mappedValue)];
                    } else {
                        const matchKeys = ["id", "value", "code", "key"];
                        const detectKey = Object.keys(value).find((k) =>
                            matchKeys.includes(k)
                        );
                        if (detectKey) {
                            values = [String(value[detectKey])];
                        }
                    }
                } else if (value != null) {
                    values = [String(value)];
                }

                // Set selected
                Array.from(field.options).forEach((opt) => {
                    opt.selected = values.includes(opt.value);
                });

                $(field).val(values);
                initSumoSelect($(field));
            } else if (tag === "textarea") {
                // Áp dụng formatter cho textarea nếu cần
                const formatter = inputValueFormatter[key];
                const formattedValue = formatter
                    ? formatter(value)
                    : value ?? "";
                field.value = formattedValue;
            }
        });

        if (typeof afterAutoMatchFieldAndFillPatchFormDone == "function") {
            afterAutoMatchFieldAndFillPatchFormDone();
        }
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
