const type = document.getElementById("type");

const toggleFileUrlSelect = () => {
    const value = type.value;
    const select = document.querySelector('select[name="extensions[]"]');
    const selectCol = select?.closest(".col-md-3");

    destroySumoSelect?.($(select));
    if (select) select.value = "";

    if (value === "url") {
        select?.removeAttribute("required");
        if (selectCol) selectCol.hidden = true;
    } else {
        select?.setAttribute("required", "true");
        if (selectCol) selectCol.hidden = false;
        initSumoSelect?.($(select));
    }
};

type.addEventListener("change", toggleFileUrlSelect);
document.addEventListener("DOMContentLoaded", toggleFileUrlSelect);
