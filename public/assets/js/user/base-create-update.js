const numberInputs=[
    document.getElementById("salary-level"),
    document.getElementById("violation-penalty"),
    document.getElementById("allowance-meal"),
    document.getElementById("allowance-contact"),
    document.getElementById("allowance-position"),
    document.getElementById("allowance-fuel"),
    document.getElementById("allowance-transport"),
];

numberInputs.forEach((input) => {
    ["input", "paste", "change", "blur"].forEach((evt) => {
        input.addEventListener(evt, () => updateFormattedSpan(input));
    });
});

document.addEventListener("DOMContentLoaded",()=>{
    numberInputs.forEach((input) => {
        updateFormattedSpan(input);
    });
})
