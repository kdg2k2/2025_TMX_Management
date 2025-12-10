selectValueMapping = {
    "extensions[]": (item) => item.extension_id,
};
inputValueFormatter = {};

const afterAutoMatchFieldAndFillPatchFormDone = () => {
    setTimeout(() => {
        autoFillSelect(
            document.querySelector('select[name="extensions[]"]'),
            "extensions",
            $data.extensions
        );
    }, 1000);
};
