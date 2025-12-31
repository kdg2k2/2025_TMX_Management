document.addEventListener("DOMContentLoaded", () => {
    ["input", "paste", "change", "blur"].forEach((evt) => {
        [
            document.getElementById("current-km"),
            document.getElementById("maintenance-km"),
        ].forEach((element) => {
            element.addEventListener(evt, () =>
                updateFormattedSpan(element, null)
            );
        });
    });
});
