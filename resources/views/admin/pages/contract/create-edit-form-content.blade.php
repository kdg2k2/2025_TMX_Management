    @php
        $renderOpt = function ($array, $addEmptyOpt = true, $key = 'id', $value = 'name') {
            return implode(
                '',
                array_merge(
                    $addEmptyOpt ? ["<option value=''>Chọn</option>"] : [],
                    array_map(function ($user) use ($key, $value) {
                        return "<option value='{$user[$key]}'>{$user[$value]}</option>";
                    }, $array),
                ),
            );
        };
    @endphp

    <div class="my-1 col-md-4">
        <div class="form-group">
            <label>
                Tên hợp đồng
            </label>
            <input class="form-control" type="text" name="name" required>
        </div>
    </div>
    <div class="my-1 col-md-4">
        <div class="form-group">
            <label>
                Tên viết tắt
            </label>
            <input class="form-control" type="text" name="short_name" required>
        </div>
    </div>
    <div class="my-1 col-md-4">
        <div class="form-group">
            <label>
                Năm
            </label>
            <input class="form-control" type="number" name="year" required>
        </div>
    </div>
    <div class="my-1 col-md-4">
        <div class="form-group">
            <label>
                Số hợp đồng
            </label>
            <input class="form-control" type="text" name="contract_number" required>
        </div>
    </div>
    <div class="my-1 col-md-4">
        <div class="form-group">
            <label>
                Loại hợp đồng
            </label>
            <select class="form-control" name="type_id" required>
                {!! $renderOpt($types) !!}
            </select>
        </div>
    </div>
    <div class="my-1 col-md-4">
        <div class="form-group">
            <label>
                Nhà đầu tư
            </label>
            <select class="form-control" name="investor_id" required>
                {!! $renderOpt($investors) !!}
            </select>
        </div>
    </div>

    <div class="my-1 col-md-4">
        <div class="form-group">
            <label for="contract_value">
                Giá trị hợp đồng
            </label>
            <input class="form-control" type="text" name="contract_value" id="contract_value">
        </div>
    </div>
    <div class="my-1 col-md-4">
        <div class="form-group">
            <label for="vat_rate">
                Giá trị % thuế
            </label>
            <input class="form-control" type="text" name="vat_rate" id="vat_rate">
        </div>
    </div>
    <div class="my-1 col-md-4">
        <div class="form-group">
            <label for="vat_amount">
                Tiền thuế
            </label>
            <input class="form-control" type="text" name="vat_amount" id="vat_amount" readonly>
        </div>
    </div>

    <div class="my-1 col-md-4">
        <div class="form-group">
            <label>
                Người hướng dẫn
            </label>
            <select class="form-control" name="instructor_id" required>
                {!! $renderOpt($users) !!}
            </select>
        </div>
    </div>
    <div class="my-1 col-md-4">
        <div class="form-group">
            <label>
                Phụ trách chuyên môn
            </label>
            <select class="form-control" name="professional_ids[]" multiple required>
                {!! $renderOpt($users, false) !!}
            </select>
        </div>
    </div>
    <div class="my-1 col-md-4">
        <div class="form-group">
            <label>
                Phụ trách giải ngân
            </label>
            <select class="form-control" name="disbursement_ids[]" multiple required>
                {!! $renderOpt($users, false) !!}
            </select>
        </div>
    </div>
    <div class="my-1 col-md-4">
        <div class="form-group">
            <label>
                Đầu mối kế toán
            </label>
            <select class="form-control" name="accounting_contact_id" required>
                {!! $renderOpt($users) !!}
            </select>
        </div>
    </div>
    <div class="my-1 col-md-4">
        <div class="form-group">
            <label>
                Người thực hiện SPTG
            </label>
            <select class="form-control" name="executor_user_id" required>
                {!! $renderOpt($users) !!}
            </select>
        </div>
    </div>
    <div class="my-1 col-md-4">
        <div class="form-group">
            <label>
                Người kiểm tra SPTG
            </label>
            <select class="form-control" name="inspector_user_id" required>
                {!! $renderOpt($users) !!}
            </select>
        </div>
    </div>
    <div class="my-1 col-md-4">
        <div class="form-group">
            <label>
                Người hỗ trợ thực hiện SPTG
            </label>
            <select class="form-control" name="intermediate_collaborator_ids[]">
                {!! $renderOpt($users) !!}
            </select>
        </div>
    </div>

    <div class="my-1 col-md-4">
        <div class="form-group">
            <label>
                Ngày ký hợp đồng
            </label>
            <input class="form-control" type="date" name="signed_date">
        </div>
    </div>
    <div class="my-1 col-md-4">
        <div class="form-group">
            <label>
                Ngày hợp đồng có hiệu lực
            </label>
            <input class="form-control" type="date" name="effective_date">
        </div>
    </div>
    <div class="my-1 col-md-4">
        <div class="form-group">
            <label>
                Ngày kết thúc hợp đồng
            </label>
            <input class="form-control" type="date" name="end_date">
        </div>
    </div>

    <script defer>
        document.addEventListener("DOMContentLoaded", () => {
            const contractValueInput = document.getElementById("contract_value");
            const vatRateInput = document.getElementById("vat_rate");
            const vatAmountInput = document.getElementById("vat_amount");
            let formatTimeout;

            // Chuyển chuỗi có dấu . hoặc , về số
            const unfmNumber = (value) => {
                if (!value) return 0;
                return parseFloat(value.toString().replace(/[^\d.]/g, "")) || 0;
            };

            // Lấy hoặc tạo span hiển thị format trong label
            const getLabelSpan = (input) => {
                const label = document.querySelector(`label[for='${input.id}']`);
                if (!label) return null;
                let span = label.querySelector(".formatted-value");
                if (!span) {
                    span = document.createElement("span");
                    span.className = "formatted-value text-muted fw-normal ms-1";
                    label.appendChild(span);
                }
                return span;
            };

            // Tính tiền thuế
            const calcVat = () => {
                const contractValue = unfmNumber(contractValueInput.value);
                const rate = unfmNumber(vatRateInput.value);
                if (contractValue > 0 && rate > 0) {
                    const vat = Math.round((contractValue / (1 + rate / 100)) * (rate / 100));
                    // input giữ raw value
                    vatAmountInput.value = vat.toFixed(0);
                    // label hiển thị formatted
                    const span = getLabelSpan(vatAmountInput);
                    if (span) span.textContent = `(${fmNumber(vat)})`;
                } else {
                    vatAmountInput.value = "";
                    const span = getLabelSpan(vatAmountInput);
                    if (span) span.textContent = "";
                }
            };

            // Cập nhật label hiển thị format
            const updateFormattedLabel = (input) => {
                if (input.id === "vat_rate") return; // không format phần trăm
                const span = getLabelSpan(input);
                if (span) {
                    const formatted = fmNumber(input.value);
                    span.textContent = formatted ? `(${formatted})` : "";
                }
            };

            // Lắng nghe thay đổi giá trị
            const handleInputChange = (input) => {
                clearTimeout(formatTimeout);
                calcVat();
                updateFormattedLabel(input);
                formatTimeout = setTimeout(() => {
                    calcVat();
                    updateFormattedLabel(input);
                }, 1000);
            };

            // Gắn event cho 2 input chính
            [contractValueInput, vatRateInput].forEach((input) => {
                ["input", "paste", "change", "blur"].forEach((evt) => {
                    input.addEventListener(evt, () => handleInputChange(input));
                });
            });
        });
    </script>
