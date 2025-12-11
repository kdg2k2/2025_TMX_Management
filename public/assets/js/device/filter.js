const deviceTypeId = document.getElementById("device-type-id");
const currentStatus = document.getElementById("current-status");
[deviceTypeId, currentStatus].forEach((item) => {
    item.addEventListener("change", () => {
        customDataTableFilterParams[item.getAttribute("name")] =
            item.value || "";

        loadList();
    });
});
