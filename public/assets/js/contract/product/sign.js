// Load thông tin biên bản
const loadMinuteInfo = async () => {
    try {
        const response = await http.get(apiContractProductMinuteShow, {
            id: minuteId,
        });
        const minute = response.data;

        if (!minute) throw new Error("Không tìm thấy biên bản");

        displayMinuteInfo(minute);

        const userSignature = minute.signatures?.find(
            (s) => s.user_id === authId,
        );

        if (!userSignature) {
            alertErr("Bạn không phải đối tượng được ký biên bản này!");
            return;
        }

        if (userSignature.status.original === "signed") {
            alertSuccess("Bạn đã ký biên bản này rồi!");
            $("#signature-form").addClass("d-none");
        } else {
            $("#signature-form").removeClass("d-none");
            $("#minute-id").val(minuteId);
        }

        $("#signature-container").addClass("d-none");
        $("#minute-info").removeClass("d-none");
    } catch (error) {
        alertErr(error.message || "Có lỗi xảy ra khi tải thông tin biên bản");
        $("#signature-container").html(`
            <div class="alert alert-danger">
                <i class="ti ti-alert-circle me-2"></i>
                ${error.message || "Có lỗi xảy ra"}
            </div>
        `);
    }
};

const displayMinuteInfo = (minute) => {
    $("#minute-info").html(`
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary">
                    <i class="ti ti-file-text me-2"></i>
                    Thông tin hợp đồng
                </h6>
                <table class="table table-sm">
                    <tr><td class="fw-bold">Năm:</td><td>${minute.contract?.year || ""}</td></tr>
                    <tr><td class="fw-bold">Số HĐ:</td><td>${minute.contract?.contract_number || ""}</td></tr>
                    <tr><td class="fw-bold">Tên HĐ:</td><td>${minute.contract?.name || ""}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-success">
                    <i class="ti ti-clipboard me-2"></i>
                    Thông tin biên bản
                </h6>
                <table class="table table-sm">
                    <tr><td class="fw-bold">Người tạo:</td><td>${minute.created_by?.name || ""}</td></tr>
                    <tr><td class="fw-bold">Ngày giao:</td><td>${minute.handover_date || ""}</td></tr>
                    <tr>
                        <td class="fw-bold">Trạng thái:</td>
                        <td>
                            <span class="badge bg-${minute.status?.color}">
                                ${minute.status?.converted || ""}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    `);
};

// Canvas signature
let canvas,
    ctx,
    isDrawing = false;

const initSignaturePad = () => {
    canvas = document.getElementById("signature-canvas");
    ctx = canvas.getContext("2d");

    const rect = canvas.getBoundingClientRect();
    const ratio = window.devicePixelRatio || 1;

    canvas.width = rect.width * ratio;
    canvas.height = rect.height * ratio;
    ctx.scale(ratio, ratio);

    Object.assign(ctx, {
        strokeStyle: "#000",
        lineWidth: 2,
        lineCap: "round",
        lineJoin: "round",
    });

    canvas.addEventListener("mousedown", startDrawing);
    canvas.addEventListener("mousemove", draw);
    canvas.addEventListener("mouseup", stopDrawing);
    canvas.addEventListener("mouseleave", stopDrawing);
};

const getMousePos = (e) => {
    const rect = canvas.getBoundingClientRect();
    return {
        x: e.clientX - rect.left,
        y: e.clientY - rect.top,
    };
};

const startDrawing = (e) => {
    isDrawing = true;
    const { x, y } = getMousePos(e);
    ctx.beginPath();
    ctx.moveTo(x, y);
};

const draw = (e) => {
    if (!isDrawing) return;
    const { x, y } = getMousePos(e);
    ctx.lineTo(x, y);
    ctx.stroke();
};

const stopDrawing = () => {
    isDrawing = false;
    ctx.closePath();
};

const beforeFormSubmit = (formData) => {
    if ($("#signature-type").val() === "draw") {
        formData.append("signature_data", canvas.toDataURL("image/png"));
    }
};

$("#clear-signature").on("click", () => {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
});

// Xử lý thay đổi phương thức ký
$("#signature-type").on("change", function () {
    const type = $(this).val();
    $("#draw-signature-container").addClass("d-none");
    $("#upload-signature-container").addClass("d-none");
    if (type === "draw") {
        $("#draw-signature-container").removeClass("d-none");
        initSignaturePad();
    } else if (type === "upload") {
        $("#upload-signature-container").removeClass("d-none");
    }
});

// Submit
document
    .getElementById("submit-signature-form")
    .addEventListener("submit", async (e) => {
        await handleSubmitForm(e, () => {
            setTimeout(() => {
                window.location.href = contractProductIndex;
            }, 1500);
        });
    });

// Init
minuteId
    ? loadMinuteInfo()
    : $("#signature-container").html(`
    <div class="alert alert-warning">
        <i class="ti ti-alert-triangle me-2"></i>
        Không tìm thấy thông tin biên bản. Vui lòng kiểm tra lại link.
    </div>
`);
