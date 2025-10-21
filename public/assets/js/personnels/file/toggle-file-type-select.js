const selectType = document.querySelector('select[name="type_id"]');
const inputFile = document.querySelector('input[name="path"]');
const inputFileLabel = document.getElementById("input-file-label");

const triggerSpanAndAcceptInput = () => {
    const selectedOption = selectType.options[selectType.selectedIndex];
    const record = JSON.parse(selectedOption.getAttribute("data-record")) || {};
    const extensions = (
        record?.extensions?.map((item) => "." + item?.extension?.extension) ||
        []
    ).join(",");

    inputFileLabel
        .querySelectorAll("span")
        .forEach((element) => element.remove());
    const span = getOrCreateFormattedSpan(
        inputFile,
        "file-type-extensions text-info"
    );
    span.innerText = extensions;
    inputFile.setAttribute("accept", extensions);

    inputFileLabel.appendChild(span);
};

selectType.addEventListener("change", () => {
    triggerSpanAndAcceptInput();
});
