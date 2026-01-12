const type = document.getElementById("type");
const deviceId = document.getElementById("device-id");
const toggleSelectType = (e) => {
    const el = e?.target;
    if (!el) return;
    const parentClass = ".col-md-4";

    const resetField = (field) => {
        setHiddenAndRequired(field, false, field.closest(parentClass));
        destroySumoSelect($(field));
        field.value = "";
    };

    resetField(deviceId);

    if (el.value != "personal") {
        setHiddenAndRequired(deviceId, true, deviceId.closest(parentClass));
        initSumoSelect($(deviceId));
    }
};
const afterFormSubmitDone = () => {
    type.dispatchEvent(new Event("change"));
};
type.addEventListener("change", toggleSelectType);
document.addEventListener("DOMContentLoaded", () => {
    afterFormSubmitDone();
});
