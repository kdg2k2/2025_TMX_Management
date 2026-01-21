<?php

namespace App\Http\Requests\ContractMainProduct;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            app(ExportRequest::class)->rules()
        );
    }
}
