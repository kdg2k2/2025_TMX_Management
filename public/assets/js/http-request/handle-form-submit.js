const handleSubmitForm = async (
    e,
    callbackAfterSubmit = () => {},
    resetForm = true,
    formData = null
) => {
    e.preventDefault();
    const form = e.target;
    const btnSubmit = form.querySelector('button[type="submit"]');
    const method = getFormMethod(form);
    if (!formData) formData = new FormData(form);
    const onSuccess = form.getAttribute("data-onsuccess") ?? null;
    const action = form.getAttribute("action")?.toLowerCase();

    try {
        setButtonLoading(btnSubmit, true);

        await http[method](action, formData);

        if (resetForm && method === "post") resetFormAfterSubmit(form);

        if (typeof window[onSuccess] == "function") window[onSuccess]();

        if (typeof afterSubmitDone == "function") afterSubmitDone();

        if (typeof callbackAfterSubmit == "function") callbackAfterSubmit();
    } catch (error) {
    } finally {
        setButtonLoading(btnSubmit, false);
    }
};

const setButtonLoading = (button, showOrNot, loadingText = "Đang xử lý...") => {
    if (!button) return;

    if (showOrNot) {
        button.disabled = true;
        button.dataset.originalText = button.innerHTML;
        button.innerHTML = `${loadingText}`;
    } else {
        button.disabled = false;
        button.innerHTML = button.dataset.originalText || button.innerHTML;
    }
};

const getFormMethod = (form) => {
    return (
        form.querySelector("input[name='_method']")?.value?.toLowerCase() ||
        "get"
    );
};

const resetFormAfterSubmit = (form) => {
    form.reset();
    refreshSumoSelect($(form.querySelectorAll("select")));
};
