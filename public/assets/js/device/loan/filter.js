const deviceId = document.getElementById("device-id");
const status = document.getElementById("status");
const createdBy = document.getElementById("created-by");
[deviceId, status, createdBy].forEach((item) => {
    item.addEventListener("change", () => {
        customDataTableFilterParams[item.getAttribute("name")] =
            item.value || "";
        loadList();
    });
});
