const LOADING_ELEMENT = document.getElementById("loader");

const hideLoading = () => {
    setTimeout(() => {
        LOADING_ELEMENT.classList.add("d-none");
    }, 1000);
};
const showLoading = () => LOADING_ELEMENT.classList.remove("d-none");

window.addEventListener("load", hideLoading);
