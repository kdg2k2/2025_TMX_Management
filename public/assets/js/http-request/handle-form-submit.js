const handleSubmitForm = async (e, form, callbackAfterSubmit = () => {}) => {
    e.preventDefault();
    const btnSubmit = form.querySelector('button[type="submit"]');
    const method = getFormMethod(form);
    const formData = new FormData(form);
    const onSuccess = form.getAttribute("data-onsuccess") ?? null;

    const action = form.getAttribute("action")?.toLowerCase();
    btnSubmit?.setAttribute("disabled", true);
    const res = await http[method](action, formData);

    if (res.message && method === "post") form.reset();

    if (typeof window[onSuccess] == "function") window[onSuccess]();

    if (typeof afterSubmitDone == "function") afterSubmitDone();

    if (typeof callbackAfterSubmit == "function") callbackAfterSubmit();
    btnSubmit?.removeAttribute("disabled");
};

const getFormMethod = (form) => {
    return (
        form.querySelector("input[name='_method']")?.value?.toLowerCase() ||
        "get"
    );
};
