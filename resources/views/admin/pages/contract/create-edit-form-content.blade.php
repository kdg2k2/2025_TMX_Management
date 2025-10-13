@php
    $renderOpt = function ($array, $addEmptyOpt = true, $key = 'id', $value = 'name') {
        $getDisplayValue = function ($data, $values) {
            $res = [];
            if (!is_array($values)) {
                $values = [$values];
            }
            $res = array_map(fn($i) => $data[$i] ?? '', $values);
            return implode(' - ', $res);
        };

        return implode(
            '',
            array_merge(
                $addEmptyOpt ? ["<option value=''>Chọn</option>"] : [],
                array_map(function ($item) use ($key, $value, $getDisplayValue) {
                    $display = $getDisplayValue($item, $value);
                    return "<option value='{$item[$key]}'>{$display}</option>";
                }, $array),
            ),
        );
    };
@endphp

<div class="col-12 my-1">
    <div class="card">
        <div class="card-header">
            <h6>
                Thông tin chung
            </h6>
        </div>
        <div class="card-body row">
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
                        Chủ đầu tư
                    </label>
                    <select class="form-control" name="investor_id" required>
                        {!! $renderOpt($investors, true, 'id', ['name_vi', 'name_en']) !!}
                    </select>
                </div>
            </div>
            <div class="my-1 col-md-4">
                <div class="form-group">
                    <label>
                        Tên tiếng việt - người tham chiếu của nhà đầu tư
                    </label>
                    <input class="form-control" type="text" name="vi_name_of_investor_reference_person">
                </div>
            </div>
            <div class="my-1 col-md-4">
                <div class="form-group">
                    <label>
                        Tên tiếng anh - người tham chiếu của nhà đầu tư
                    </label>
                    <input class="form-control" type="text" name="en_name_of_investor_reference_person">
                </div>
            </div>

            <div class="my-1 col-md-4">
                <div class="form-group">
                    <label>
                        Bên A
                    </label>
                    <input class="form-control" type="text" name="a_side">
                </div>
            </div>
            <div class="my-1 col-md-4">
                <div class="form-group">
                    <label>
                        Bên B
                    </label>
                    <input class="form-control" type="text" name="b_side">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-12 my-1">
    <div class="card">
        <div class="card-header">
            <h6>
                Địa điểm & Thời gian
            </h6>
        </div>
        <div class="card-body row">
            <div class="my-1 col-md-3">
                <div class="form-group">
                    <label>
                        Địa điểm
                    </label>
                    <select name="contract_scopes[]" class="form-control" required multiple>
                        {!! $renderOpt($provinces, false, 'code', 'name') !!}
                    </select>
                </div>
            </div>
            <div class="my-1 col-md-3">
                <div class="form-group">
                    <label>
                        Ngày ký hợp đồng
                    </label>
                    <input class="form-control" type="date" name="signed_date">
                </div>
            </div>
            <div class="my-1 col-md-3">
                <div class="form-group">
                    <label>
                        Ngày hợp đồng có hiệu lực
                    </label>
                    <input class="form-control" type="date" name="effective_date">
                </div>
            </div>
            <div class="my-1 col-md-3">
                <div class="form-group">
                    <label>
                        Ngày kết thúc hợp đồng
                    </label>
                    <input class="form-control" type="date" name="end_date">
                </div>
            </div>
            <div class="my-1 col-md-3">
                <div class="form-group">
                    <label>
                        Ngày hoàn thành
                    </label>
                    <input class="form-control" type="date" name="completion_date" disabled>
                </div>
            </div>
            <div class="my-1 col-md-3">
                <div class="form-group">
                    <label>
                        Ngày nghiệm thu hợp đồng
                    </label>
                    <input class="form-control" type="date" name="acceptance_date">
                </div>
            </div>
            <div class="my-1 col-md-3">
                <div class="form-group">
                    <label>
                        Ngày thanh lý hợp đồng
                    </label>
                    <input class="form-control" type="date" name="liquidation_date">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-12 my-1">
    <div class="card">
        <div class="card-header">
            <h6>
                Giá trị hợp đồng
            </h6>
        </div>
        <div class="card-body row">
            <div class="my-1 col-md-4">
                <div class="form-group">
                    <label for="contract_value">
                        Giá trị hợp đồng (đồng)
                    </label>
                    <input class="form-control" type="text" name="contract_value" id="contract_value">
                </div>
            </div>
            <div class="my-1 col-md-2">
                <div class="form-group">
                    <label for="vat_rate">
                        Giá trị % thuế (%)
                    </label>
                    <input class="form-control" type="text" name="vat_rate" id="vat_rate">
                </div>
            </div>
            <div class="my-1 col-md-6">
                <div class="form-group">
                    <label for="vat_amount">
                        Tiền thuế (đồng)
                    </label>
                    <input class="form-control bg-light" type="text" name="vat_amount" id="vat_amount" readonly>
                </div>
            </div>
            <div class="my-1 col-md-4">
                <div class="form-group">
                    <label>
                        Giá trị nghiệm thu (đồng)
                    </label>
                    <input class="form-control" type="text" name="acceptance_value" id="acceptance_value">
                </div>
            </div>
            <div class="my-1 col-md-4">
                <div class="form-group">
                    <label>
                        Giá trị thanh lý (đồng)
                    </label>
                    <input class="form-control" type="text" name="liquidation_value" id="liquidation_value">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-12 my-1">
    <div class="card">
        <div class="card-header">
            <h6>
                Nhân sự
            </h6>
        </div>
        <div class="card-body row">
            <div class="my-1 col-md-3">
                <div class="form-group">
                    <label>
                        Người hướng dẫn
                    </label>
                    <select class="form-control" name="instructor_ids[]" multiple>
                        {!! $renderOpt($users, false) !!}
                    </select>
                </div>
            </div>
            <div class="my-1 col-md-3">
                <div class="form-group">
                    <label>
                        Phụ trách chuyên môn
                    </label>
                    <select class="form-control" name="professional_ids[]" multiple>
                        {!! $renderOpt($users, false) !!}
                    </select>
                </div>
            </div>
            <div class="my-1 col-md-3">
                <div class="form-group">
                    <label>
                        Phụ trách giải ngân
                    </label>
                    <select class="form-control" name="disbursement_ids[]" multiple>
                        {!! $renderOpt($users, false) !!}
                    </select>
                </div>
            </div>
            <div class="my-1 col-md-3">
                <div class="form-group">
                    <label>
                        Đầu mối kế toán
                    </label>
                    <select class="form-control" name="accounting_contact_id">
                        {!! $renderOpt($users) !!}
                    </select>
                </div>
            </div>
            <div class="my-1 col-md-3">
                <div class="form-group">
                    <label>
                        Người thực hiện SPTG
                    </label>
                    <select class="form-control" name="executor_user_id">
                        {!! $renderOpt($users) !!}
                    </select>
                </div>
            </div>
            <div class="my-1 col-md-3">
                <div class="form-group">
                    <label>
                        Người kiểm tra SPTG
                    </label>
                    <select class="form-control" name="inspector_user_id">
                        {!! $renderOpt($users) !!}
                    </select>
                </div>
            </div>
            <div class="my-1 col-md-3">
                <div class="form-group">
                    <label>
                        Người hỗ trợ thực hiện SPTG
                    </label>
                    <select class="form-control" name="intermediate_collaborator_ids[]" multiple>
                        {!! $renderOpt($users, false) !!}
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-12 my-1">
    <div class="card">
        <div class="card-header">
            <h6>
                Trạng thái
            </h6>
        </div>
        <div class="card-body row">
            <div class="my-1 col-md-4">
                <div class="form-group">
                    <label>
                        Tình trạng hợp đồng
                    </label>
                    <select name="contract_status" class="form-control" required>
                        {!! $renderOpt($contract_status, true, 'original', 'converted') !!}
                    </select>
                </div>
            </div>
            <div class="my-1 col-md-4">
                <div class="form-group">
                    <label>
                        Tình trạng SPTG
                    </label>
                    <select name="intermediate_product_status" class="form-control" required>
                        {!! $renderOpt($intermediate_product_status, true, 'original', 'converted') !!}
                    </select>
                </div>
            </div>
            <div class="my-1 col-md-4">
                <div class="form-group">
                    <label>
                        Tình trạng hồ sơ tài chính
                    </label>
                    <select name="financial_status" class="form-control" required>
                        {!! $renderOpt($financial_status, true, 'original', 'converted') !!}
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-12 my-1">
    <div class="card">
        <div class="card-header">
            <h6>
                Khác
            </h6>
        </div>
        <div class="card-body row">
            <div class="my-1 col-md-4">
                <div class="form-group">
                    <label>
                        Là hợp đồng đặc biệt?
                    </label>
                    <select name="is_special" class="form-control" required>
                        <option value="0">Không</option>
                        <option value="1">Có</option>
                    </select>
                </div>
            </div>

            <div class="my-1 col-md-4">
                <div class="form-group">
                    <label>
                        Là hợp đồng nhiều năm?
                    </label>
                    <select name="is_contract_many_year" id="is_contract_many_year" class="form-control" required>
                        <option value="0">Không</option>
                        <option value="1">Có</option>
                    </select>
                </div>
            </div>
            <div class="my-1 col-md-4" hidden>
                <div class="form-group">
                    <label>
                        Các năm thuộc nhiều năm
                    </label>
                    <select name="many_years[]" id="many_years" class="form-control" multiple>
                        @php
                            $years = range(2023, date('Y') + 10);
                        @endphp
                        @foreach ($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="my-1 col-md-4">
                <div class="form-group">
                    <label>
                        Ghi chú
                    </label>
                    <textarea name="note" rows="1" class="form-control"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<script defer>
    const contractValueInput = document.getElementById("contract_value");
    const vatRateInput = document.getElementById("vat_rate");
    const vatAmountInput = document.getElementById("vat_amount");
    let formatTimeout;
    const selectManyYear = document.getElementById("is_contract_many_year");
    const colManyYear = document.querySelector("#many_years").closest(".col-md-4");
    const selectYears = document.getElementById("many_years");

    const toggleManyYear = () => {
        const isMany = selectManyYear.value === "1";
        colManyYear.hidden = !isMany;
        if (!isMany) {
            const select = $(selectYears);
            destroySumoSelect(select);
            // Bỏ chọn tất cả option nếu không phải hợp đồng nhiều năm
            Array.from(selectYears.options).forEach(opt => opt.selected = false);
            initSumoSelect(select);
        }
    };

    // Lấy hoặc tạo span hiển thị format trong label
    const getLabelSpan = (input) => {
        const label = document.querySelector(`label[for='${input.id}']`);
        if (!label) return null;
        let span = label.querySelector(".formatted-value");
        if (!span) {
            span = document.createElement("span");
            span.className = "formatted-value text-muted text-info fw-normal ms-1";
            label.appendChild(span);
        }
        return span;
    };

    const clearLabelSpans = () => {
        document.querySelectorAll('.formatted-value').forEach(el => el.remove());
    }

    // Tính tiền thuế
    const calcVat = () => {
        const contractValue = contractValueInput.value;
        const rate = vatRateInput.value;
        if (contractValue > 0 && rate > 0) {
            const vat = Math.round((contractValue / (1 + (rate / 100))) * (rate / 100));
            // input giữ raw value
            vatAmountInput.value = vat.toFixed(0);
            // label hiển thị formatted
            const span = getLabelSpan(vatAmountInput);
            if (span) span.textContent =
                `${fmNumber(contractValue)}/(1 + ${rate}%) * ${rate}% = ${fmNumber(vat)} (Đã làm tròn)`;
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
            span.textContent = formatted ? `${formatted}` : "";
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

    selectManyYear.addEventListener("change", toggleManyYear);
    document.addEventListener("DOMContentLoaded", () => {
        toggleManyYear();

        applyIntegerValidation([
            'contract_value',
            'vat_rate',
            'acceptance_value',
            'liquidation_value',
        ]);

        // Gắn event cho 2 input chính
        [contractValueInput, vatRateInput].forEach((input) => {
            ["input", "paste", "change", "blur"].forEach((evt) => {
                input.addEventListener(evt, () => handleInputChange(input));
            });
        });
    });
</script>
