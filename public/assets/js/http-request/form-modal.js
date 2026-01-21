const openModalBase = (btn, options) => {
    const { modal, form, inputs = {}, afterShow } = options;

    const title = btn.getAttribute("aria-label");
    const action = btn?.dataset?.href || null;
    const onsuccess = btn?.dataset?.onsuccess || null;
    const titleEl = modal.querySelector(".modal-title");
    if (titleEl) titleEl.innerHTML = title;

    if (action && form) {
        form.action = action;
        if (onsuccess) form.dataset.onsuccess = onsuccess;

        // Set giá trị các input động
        Object.entries(inputs).forEach(([selector, value]) => {
            form.querySelector(selector).value = value;
        });
    }

    showModal(modal);
    if (typeof afterShow == 'function') afterShow();
};
