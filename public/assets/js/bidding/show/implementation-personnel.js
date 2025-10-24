const implementationPersonnelForm = document.getElementById(
    "implementation-personnel-form"
);
const implementationPersonnelCloneRow = document.getElementById(
    "implementation-personnel-clone-row"
);
const btnAddRowImplementationPersonnel =
    implementationPersonnelCloneRow.querySelector("button");

const loadAndFillSelectPersonnelUnits = async () => {
    const res = await http.get(listPersonnelUnitsUrl, {}, null, false);
    if (res.data) {
        fillSelectElement(
            implementationPersonnelCloneRow.querySelector(
                ".implementation-personnel-unit"
            ),
            res.data,
            "id",
            ["short_name", "name"]
        );

        fillSelectPersonels(
            implementationPersonnelCloneRow.querySelector(
                ".implementation-personnel"
            )
        );

        fillSelectPersonelFiles(
            implementationPersonnelCloneRow.querySelector(
                ".implementation-personnel-file"
            )
        );
    }
};

const fillSelectPersonels = (select, data = []) => {
    fillSelectElement(select, data, "id", ["name", "educational_level"]);
};

const fillSelectPersonelFiles = (select, data = []) => {
    fillSelectElement(
        select,
        data,
        "id",
        ["type.name", "updated_at"],
        "",
        false
    );
};

const loadAndFillSelectPersonels = async (value, select) => {
    const res = await http.get(
        listPersonnelsUrl,
        {
            personnel_unit_id: value || "",
        },
        null,
        false
    );
    if (res.data) fillSelectPersonels(select, res.data);
};

const loadAndFillSelectPersonelFiles = async (value, select) => {
    const res = await http.get(
        listPersonnelFilesUrl,
        {
            personnel_id: value | "",
        },
        null,
        false
    );
    if (res.data) fillSelectPersonelFiles(select, res.data);
};

// lấy tất cả select
const getImplementationPersonnelSelects = (jquery = false) => {
    const selector = "select";
    if (jquery) return $(implementationPersonnelForm).find(selector);
    return implementationPersonnelForm.querySelectorAll(selector);
};

// reindex toàn bộ dòng trước khi thêm dòng mới
const reindexPersonnelRows = () => {
    const rows = implementationPersonnelForm.querySelectorAll(".col-12.row");

    rows.forEach((row, i) => {
        row.querySelectorAll("select[name], input[name]").forEach((sel) => {
            sel.name = sel.name.replace(/\[\d+\]/, `[${i}]`);
        });
    });
};

// lấy index cao nhất hiện có
const getMaxPersonnelIndex = () => {
    const selects = getImplementationPersonnelSelects();
    const indexes = [];

    selects.forEach((select) => {
        const match = select.name.match(/\[(\d+)\]/);
        if (match) indexes.push(Number(match[1]));
    });

    return indexes.length ? Math.max(...indexes) : -1;
};

const renderRowImplementationPersonnel = () => {
    destroySumoSelect(getImplementationPersonnelSelects(true));

    // Reindex trước khi clone
    reindexPersonnelRows();

    const clone = implementationPersonnelCloneRow.cloneNode(true);
    clone.removeAttribute("id");

    // Lấy index mới (sau khi reindex thì max luôn là dòng cuối)
    const newIndex = getMaxPersonnelIndex() + 1;

    // Cập nhật name
    clone.querySelectorAll("select[name], input[name]").forEach((el) => {
        el.name = el.name.replace(/\[\d+\]/, `[${newIndex}]`);

        // Reset value trừ khi là input hidden
        if (!(el.tagName === "INPUT" && el.type === "hidden")) {
            el.value = "";
        }
    });

    // Cấu hình nút xóa
    const btn = clone.querySelector("button");
    btn.classList.remove("btn-success");
    btn.classList.add("btn-danger", "implementation-personnel-remove-row");
    btn.setAttribute("title", "Xóa dòng");
    btn.onclick = (ev) => {
        ev.preventDefault();
        clone.remove();
        // sau khi xóa, reindex lại luôn
        reindexPersonnelRows();
    };

    const icon = btn.querySelector("i");
    icon.classList.remove("ti-plus");
    icon.classList.add("ti-trash");

    // Gắn clone trước nút submit
    const submitRow =
        implementationPersonnelForm.querySelector(".btn-submit-row");
    implementationPersonnelForm.insertBefore(clone, submitRow);

    initSumoSelect(getImplementationPersonnelSelects(true));

    return clone;
};

const waitForOptionsLoaded = (select, expectedValue, timeout = 5000) => {
    return new Promise((resolve, reject) => {
        const start = performance.now();

        const check = () => {
            const found = [...select.options].some(
                (opt) => opt.value == expectedValue
            );
            if (found) return resolve();

            if (performance.now() - start > timeout)
                return reject(
                    new Error(`Timeout waiting for options of ${select.name}`)
                );

            requestAnimationFrame(check);
        };

        check();
    });
};

const restoreSelectedSaved = async () => {
    if (!$data?.bidding_implementation_personnel?.length) return;

    for (const [
        index,
        value,
    ] of $data.bidding_implementation_personnel.entries()) {
        // Tạo dòng container
        const container =
            index === 0
                ? implementationPersonnelCloneRow
                : renderRowImplementationPersonnel();

        const unitSelect = container.querySelector(
            ".implementation-personnel-unit"
        );
        const personnelSelect = container.querySelector(
            ".implementation-personnel"
        );
        const fileSelect = container.querySelector(
            ".implementation-personnel-file"
        );
        const jobTitleSelect = container.querySelector(
            ".implementation-personnel-jobtitle"
        );

        // 1️⃣ Chọn Unit
        if (value?.personnel?.personnel_unit_id) {
            unitSelect.value = String(value.personnel.personnel_unit_id);
            unitSelect.dispatchEvent(new Event("change", { bubbles: true }));
            await waitForOptionsLoaded(personnelSelect, value.personnel_id);
        }

        // 2️⃣ Chọn Personnel
        if (value?.personnel_id) {
            personnelSelect.value = String(value.personnel_id);
            personnelSelect.dispatchEvent(
                new Event("change", { bubbles: true })
            );
            const firstFileId = value?.files?.[0]?.personnel_file_id;
            if (firstFileId) {
                await waitForOptionsLoaded(fileSelect, firstFileId);
            }
        }

        // 3️⃣ Chọn Files (đa lựa chọn)
        if (value?.files?.length) {
            const fileIds = value.files.map((f) => String(f.personnel_file_id));
            fileIds.forEach((id) => {
                const opt = fileSelect.querySelector(`option[value="${id}"]`);
                if (opt) opt.selected = true;
            });
            fileSelect.dispatchEvent(new Event("change", { bubbles: true }));
        }

        // 4️⃣ Chọn Job Title (không cần chờ gì)
        if (value?.job_title) {
            jobTitleSelect.value = value.job_title;
            jobTitleSelect.dispatchEvent(
                new Event("change", { bubbles: true })
            );
        }
    }

    refreshSumoSelect(getImplementationPersonnelSelects(true));
};

btnAddRowImplementationPersonnel.addEventListener("click", () => {
    renderRowImplementationPersonnel();
});

implementationPersonnelForm.addEventListener("change", (e) => {
    const target = e.target;
    const closestRow = target.closest(".col-12");

    if (target.classList.contains("implementation-personnel-unit"))
        loadAndFillSelectPersonels(
            target.value,
            closestRow.querySelector("select.implementation-personnel")
        );
    if (target.classList.contains("implementation-personnel"))
        loadAndFillSelectPersonelFiles(
            target.value,
            closestRow.querySelector("select.implementation-personnel-file")
        );
});

implementationPersonnelForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, implementationPersonnelForm, () => {}, false);
});

window.tabImplementationPersonnel = async () => {
    await loadAndFillSelectPersonnelUnits();
    restoreSelectedSaved();
};

document.addEventListener("DOMContentLoaded", () => {
    tabImplementationPersonnel();
});
