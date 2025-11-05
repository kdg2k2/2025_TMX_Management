const createModal = (element) => {
    return bootstrap.Modal.getOrCreateInstance(element, {
        backdrop: "static",
        keyboard: false,
    });
};

const showModal = (element) => {
    const modal = createModal(element);
    modal?.show();
};

const hideModal = (element) => {
    const modal = bootstrap.Modal.getInstance(element);
    modal?.hide();
};
