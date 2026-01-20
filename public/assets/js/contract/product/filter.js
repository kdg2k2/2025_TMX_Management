["minute-status"].forEach((item) => {
    document.getElementById(item)?.addEventListener("change", (e) => {
        customDataTableFilterParams[e.target.getAttribute("name")] =
            e.target.value || "";

        loadList();
    });
});
