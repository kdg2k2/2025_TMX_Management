const typeProgram = document.querySelector('[name="type_program"]');
const contractId = document.querySelector('[name="contract_id"]');
const otherProgram = document.querySelector('[name="other_program"]');

const toggleTypeProgram = () => {
    destroySumoSelect($(contractId));
    // Ẩn & bỏ required mặc định
    contractId.removeAttribute("required");
    contractId.value = "";
    contractId.closest(".col-md-4").hidden = true;
    otherProgram.removeAttribute("required");
    otherProgram.value = "";
    otherProgram.closest(".col-md-4").hidden = true;

    // Kiểm tra giá trị được chọn
    if (typeProgram.value === "contract") {
        contractId.setAttribute("required", "required");
        contractId.closest(".col-md-4").hidden = false;
    } else {
        otherProgram.setAttribute("required", "required");
        otherProgram.closest(".col-md-4").hidden = false;
    }
    initSumoSelect($(contractId));
};

// Khi user thay đổi type_program
typeProgram.addEventListener("change", toggleTypeProgram);

// Khi trang load xong
document.addEventListener("DOMContentLoaded", toggleTypeProgram);
