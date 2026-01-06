[
    document.getElementById("vehicle-id"),
    document.getElementById("status"),
    document.getElementById("vehicle-status-return"),
    document.getElementById("created-by"),
].forEach((item) => {
    item.addEventListener("change", () => {
        customDataTableFilterParams[item.getAttribute("name")] =
            item.value || "";
        loadList();
    });
});
