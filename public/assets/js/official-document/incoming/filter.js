[
    document.getElementById("official-document-type-id"),
    document.getElementById("program-type"),
    document.getElementById("status"),
].forEach((item) => {
    item.addEventListener("change", () => {
        customDataTableFilterParams[item.getAttribute("name")] =
            item.value || "";

        loadList();
    });
});
