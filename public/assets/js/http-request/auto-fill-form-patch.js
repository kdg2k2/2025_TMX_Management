var selectValueMapping = (inputValueFormatter = {});

// Tự động fill form nếu có dữ liệu và là update (PATCH)
const autoMatchFieldAndFillPatchForm = (form, method, data) => {
    if (method === "patch" && data && typeof data === "object") {
        const fields = form.querySelectorAll("[name]");
        fields.forEach((field) => {
            const key = field.name.replace(/\[\]$/, ""); // bỏ [] nếu có
            const tag = field.tagName.toLowerCase();

            let value =
                data && key in data
                    ? data[key]
                    : inputValueFormatter?.[key]?.() ?? null;

            if (tag === "input") {
                autoFillInput(field, key, value);
            } else if (tag === "select") {
                value =
                    data && key in data
                        ? data[key]
                        : selectValueMapping?.[key]?.() ?? null;

                autoFillSelect(field, key, value);
            } else if (tag === "textarea") {
                autoFillTextArea(field, key, value);
            }
        });

        if (typeof afterAutoMatchFieldAndFillPatchFormDone == "function") {
            afterAutoMatchFieldAndFillPatchFormDone();
        }
    }
};

const autoFillInput = (field, key, value) => {
    const type = field.getAttribute("type")?.toLowerCase() || "text";

    if (["file", "password", "hidden"].includes(type)) return;

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
        const formattedValue = formatter ? formatter(value) : value ?? "";
        field.value = formattedValue;
    }
};

const autoFillSelect = (field, key, value) => {
    destroySumoSelect($(field));
    let values = [];

    const mapper = selectValueMapping[field.name] || selectValueMapping[key];

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
                const detectKey = matchKeys.find((k) => k in value[0]);
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
};

const autoFillTextArea = (field, key, value) => {
    // Áp dụng formatter cho textarea nếu cần
    const formatter = inputValueFormatter[key];
    const formattedValue = formatter ? formatter(value) : value ?? "";
    field.value = formattedValue;
};
