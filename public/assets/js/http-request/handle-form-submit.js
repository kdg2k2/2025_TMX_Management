const handleSubmitForm = async (
    e,
    form,
    callbackAfterSubmit = () => {},
    resetForm = true
) => {
    e.preventDefault();
    const btnSubmit = form.querySelector('button[type="submit"]');
    const method = getFormMethod(form);
    const formData = new FormData(form);
    const onSuccess = form.getAttribute("data-onsuccess") ?? null;
    const action = form.getAttribute("action")?.toLowerCase();

    try {
        btnSubmit?.setAttribute("disabled", true);

        const res = await http[method](action, formData);
        if (resetForm && method === "post") {
            resetFormAfterSubmit(form);
        }

        if (typeof window[onSuccess] == "function") window[onSuccess]();

        if (typeof afterSubmitDone == "function") afterSubmitDone();

        if (typeof callbackAfterSubmit == "function") callbackAfterSubmit();
    } catch (error) {
    } finally {
        btnSubmit?.removeAttribute("disabled");
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
    refreshSumoSelect($(form).find("select"));
};
