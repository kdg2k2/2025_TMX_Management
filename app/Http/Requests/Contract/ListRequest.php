<?php

namespace App\Http\Requests\Contract;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'intermediate_product_status' => 'nullable|in:completed,in_progress,pending_review,multi_year,technical_done,has_issues,issues_recorded',
                'investor_id' => 'nullable|exists:contract_investors,id',
                'year' => 'nullable|integer',
            ]
        );
    }
}
