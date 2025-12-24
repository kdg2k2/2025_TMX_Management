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
            <label>Loại hợp đồng</label>
            <select class="form-control" name="type_id" required>
                <x-select-options :items="$types" />
            </select>
        </div>

        <div class="my-1 col-md-4">
            <label>Chủ đầu tư</label>
            <select class="form-control" name="investor_id" required>
                <x-select-options :items="$investors" :value-fields="['name_vi', 'name_en']" />
            </select>
        </div>

        <div class="my-1 col-md-4">
            <div class="form-group">
                <label>
                    Tên người tham chiếu của nhà đầu tư (tiếng việt)
                </label>
                <input class="form-control" type="text" name="vi_name_of_investor_reference_person">
            </div>
        </div>
        <div class="my-1 col-md-4">
            <div class="form-group">
                <label>
                    Tên người tham chiếu của nhà đầu tư (tiếng anh)
                </label>
                <input class="form-control" type="text" name="en_name_of_investor_reference_person">
            </div>
        </div>

        <div class="my-1 col-md-4">
            <div class="form-group">
                <label>
                    Tên hợp đồng (tiếng anh)
                </label>
                <input class="form-control" type="text" name="name_en">
            </div>
        </div>
        <div class="my-1 col-md-4">
            <div class="form-group">
                <label>
                    Mục tiêu (tiếng việt)
                </label>
                <textarea name="target_vi" class="form-control" rows="1"></textarea>
            </div>
        </div>
        <div class="my-1 col-md-4">
            <div class="form-group">
                <label>
                    Mục tiêu (tiếng anh)
                </label>
                <textarea name="target_en" class="form-control" rows="1"></textarea>
            </div>
        </div>
        <div class="my-1 col-md-4">
            <div class="form-group">
                <label>
                    Hoạt động chính (tiếng việt)
                </label>
                <textarea name="main_activities_vi" class="form-control" rows="1"></textarea>
            </div>
        </div>
        <div class="my-1 col-md-4">
            <div class="form-group">
                <label>
                    Hoạt động chính (tiếng anh)
                </label>
                <textarea name="main_activities_en" class="form-control" rows="1"></textarea>
            </div>
        </div>
        <div class="my-1 col-md-4">
            <div class="form-group">
                <label>
                    Sản phẩm (tiếng việt)
                </label>
                <textarea name="product_vi" class="form-control" rows="1"></textarea>
            </div>
        </div>
        <div class="my-1 col-md-4">
            <div class="form-group">
                <label>
                    Sản phẩm (tiếng anh)
                </label>
                <textarea name="product_en" class="form-control" rows="1"></textarea>
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
            <label>Địa điểm</label>
            <select name="scopes[]" class="form-control" multiple required>
                <x-select-options :items="$provinces" key-field="code" value-fields="name" :empty-option="false" />
            </select>
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
        <div class="my-1 col-md-6">
            <div class="form-group">
                <label>
                    Giá trị hợp đồng(vnđ)
                </label>
                <input class="form-control" type="text" name="contract_value" id="contract_value">
            </div>
        </div>
        <div class="my-1 col-md-6">
            <div class="form-group">
                <label>
                    Mức thuế(%)
                </label>
                <input class="form-control" type="text" name="vat_rate" id="vat_rate">
            </div>
        </div>
        <div class="my-1 col-md-6">
            <div class="form-group">
                <label>
                    VAT(vnđ)
                </label>
                <input class="form-control bg-light" type="text" name="vat_amount" id="vat_amount" readonly>
            </div>
        </div>
        <div class="my-1 col-md-6">
            <div class="form-group">
                <label>
                    Giá trị nghiệm thu(vnđ)
                </label>
                <input class="form-control" type="text" name="acceptance_value" id="acceptance_value" disabled>
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
            <label>Người hướng dẫn</label>
            <select class="form-control" name="instructors[]" multiple>
                <x-select-options :items="$users" :empty-option="false" />
            </select>
        </div>

        <div class="my-1 col-md-3">
            <label>Phụ trách chuyên môn</label>
            <select class="form-control" name="professionals[]" multiple>
                <x-select-options :items="$users" :empty-option="false" />
            </select>
        </div>

        <div class="my-1 col-md-3">
            <label>Phụ trách giải ngân</label>
            <select class="form-control" name="disbursements[]" multiple>
                <x-select-options :items="$users" :empty-option="false" />
            </select>
        </div>

        <div class="my-1 col-md-3">
            <label>Đầu mối kế toán</label>
            <select class="form-control" name="accounting_contact_id">
                <x-select-options :items="$users" />
            </select>
        </div>

        <div class="my-1 col-md-3">
            <label>Người thực hiện SPTG</label>
            <select class="form-control" name="executor_user_id">
                <x-select-options :items="$users" />
            </select>
        </div>

        <div class="my-1 col-md-3">
            <label>Người kiểm tra SPTG</label>
            <select class="form-control" name="inspector_user_id">
                <x-select-options :items="$users" />
            </select>
        </div>

        <div class="my-1 col-md-3">
            <label>Người hỗ trợ thực hiện SPTG</label>
            <select class="form-control" name="intermediate_collaborators[]" multiple>
                <x-select-options :items="$users" :empty-option="false" />
            </select>
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
            <label>Tình trạng hợp đồng</label>
            <select name="contract_status" class="form-control" required>
                <x-select-options :items="$contract_status" key-field="original" value-fields="converted" />
            </select>
        </div>

        <div class="my-1 col-md-4">
            <label>Tình trạng SPTG</label>
            <select name="intermediate_product_status" class="form-control" required>
                <x-select-options :items="$intermediate_product_status" key-field="original" value-fields="converted" />
            </select>
        </div>

        <div class="my-1 col-md-4">
            <label>Tình trạng hồ sơ tài chính</label>
            <select name="financial_status" class="form-control" required>
                <x-select-options :items="$financial_status" key-field="original" value-fields="converted" />
            </select>
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

        <div class="my-1 col-md-4">
            <div class="form-group">
                <label>
                    Link driver
                </label>
                <input type="text" class="form-control" name="ggdrive_link">
            </div>
        </div>
    </div>
</div>
