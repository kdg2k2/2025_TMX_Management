[
    document.getElementById("device-id"),
    document.getElementById("status"),
    document.getElementById("device-status-return"),
    document.getElementById("created-by"),
].forEach((item) => {
    item.addEventListener("change", () => {
        customDataTableFilterParams[item.getAttribute("name")] =
            item.value || "";
        loadList();
    });
});
