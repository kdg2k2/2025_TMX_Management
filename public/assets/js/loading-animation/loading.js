const LOADING_ELEMENT = document.getElementById("loader");

const hideLoading = () => {
    setTimeout(() => {
        LOADING_ELEMENT.classList.add("d-none");
    }, 100);
};
const showLoading = () => {
    setTimeout(() => {
        LOADING_ELEMENT.classList.remove("d-none");
    }, 100);
};

window.addEventListener("load", hideLoading);
