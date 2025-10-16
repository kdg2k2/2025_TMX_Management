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
                <select name="scopes[]" class="form-control" required multiple>
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
                <input class="form-control" type="date" name="completion_date">
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
                    Giá trị hợp đồng(vnđ)
                </label>
                <input class="form-control" type="text" name="contract_value" id="contract_value">
            </div>
        </div>
        <div class="my-1 col-md-2">
            <div class="form-group">
                <label for="vat_rate">
                    Mức thuế(%)
                </label>
                <input class="form-control" type="text" name="vat_rate" id="vat_rate">
            </div>
        </div>
        <div class="my-1 col-md-6">
            <div class="form-group">
                <label for="vat_amount">
                    VAT(vnđ)
                </label>
                <input class="form-control bg-light" type="text" name="vat_amount" id="vat_amount" readonly>
            </div>
        </div>
        <div class="my-1 col-md-4">
            <div class="form-group">
                <label>
                    Giá trị nghiệm thu (vnđ)
                </label>
                <input class="form-control" type="text" name="acceptance_value" id="acceptance_value">
            </div>
        </div>
        <div class="my-1 col-md-4">
            <div class="form-group">
                <label>
                    Giá trị thanh lý (vnđ)
                </label>
                <input class="form-control" type="text" name="liquidation_value" id="liquidation_value">
            </div>
        </div>
    </div>
</div>

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
                <select class="form-control" name="instructors[]" multiple>
                    {!! $renderOpt($users, false) !!}
                </select>
            </div>
        </div>
        <div class="my-1 col-md-3">
            <div class="form-group">
                <label>
                    Phụ trách chuyên môn
                </label>
                <select class="form-control" name="professionals[]" multiple>
                    {!! $renderOpt($users, false) !!}
                </select>
            </div>
        </div>
        <div class="my-1 col-md-3">
            <div class="form-group">
                <label>
                    Phụ trách giải ngân
                </label>
                <select class="form-control" name="disbursements[]" multiple>
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
                <select class="form-control" name="intermediate_collaborators[]" multiple>
                    {!! $renderOpt($users, false) !!}
                </select>
            </div>
        </div>
    </div>
</div>

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

        <div class="my-1 col-md-4">
            <div class="form-group">
                <label>
                    File Full (.pdf)
                </label>
                <input type="file" class="form-control" accept=".pdf" name="path_file_full">
            </div>
        </div>
        <div class="my-1 col-md-4">
            <div class="form-group">
                <label>
                    File Short (.pdf)
                </label>
                <input type="file" class="form-control" accept=".pdf" name="path_file_short">
            </div>
        </div>
    </div>
</div>
