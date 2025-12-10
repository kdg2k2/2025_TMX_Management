const LOADING_ELEMENT = document.getElementById("loader");

const hideLoading = () => {
    if (activeFetchCount > 0) return; // đang fetch → không hide

    setTimeout(() => {
        if (activeFetchCount === 0) {
            LOADING_ELEMENT.classList.add("d-none");
        }
    }, 300);
};

const showLoading = () => LOADING_ELEMENT.classList.remove("d-none");

window.addEventListener("load", hideLoading);
