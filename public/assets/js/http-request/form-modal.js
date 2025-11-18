const openModalBase = (btn, options) => {
    const { modal, form, inputs = {}, afterShow } = options;

    const title = btn.getAttribute("aria-label");
    const action = btn.dataset.href;
    const onsuccess = btn.dataset.onsuccess;

    if (action && onsuccess) {
        const titleEl = modal.querySelector(".modal-title");
        if (titleEl) titleEl.innerHTML = title;

        form.action = action;
        form.dataset.onsuccess = onsuccess;

        // Set giá trị các input động
        Object.entries(inputs).forEach(([selector, value]) => {
            form.querySelector(selector).value = value;
        });

        showModal(modal);

        if (afterShow) afterShow();
    }
};
