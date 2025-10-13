<?php

namespace App\Http\Requests\Contract;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'created_by' => $this->user()->id,
            'is_special' => $this->boolean('is_special'),
            'is_contract_many_year' => $this->boolean('is_contract_many_year'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'short_name' => 'required|max:255|unique:contracts,short_name',
            'year' => 'required|integer|min:1',
            'contract_number' => 'required|max:255|unique:contracts,contract_number',
            'created_by' => 'required|exists:users,id',
            'instructors' => 'nullable|array',
            'instructors.*' => 'nullable|exists:users,id',
            'accounting_contact_id' => 'nullable|exists:users,id',
            'inspector_user_id' => 'nullable|exists:users,id',
            'executor_user_id' => 'nullable|exists:users,id',
            'type_id' => 'required|exists:contract_types,id',
            'investor_id' => 'nullable|exists:contract_investors,id',
            'contract_value' => 'nullable|numeric|min:0',
            'vi_name_of_investor_reference_person' => 'nullable|string|max:255',
            'en_name_of_investor_reference_person' => 'nullable|string|max:255',
            'vat_rate' => 'nullable|numeric|min:0',
            'vat_amount' => 'nullable|numeric|min:0',
            'acceptance_value' => 'nullable|numeric|min:0',
            'liquidation_value' => 'nullable|numeric|min:0',
            'signed_date' => 'nullable|date_format:Y-m-d',
            'effective_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d',
            'completion_date' => 'nullable|date_format:Y-m-d',
            'acceptance_date' => 'nullable|date_format:Y-m-d',
            'liquidation_date' => 'nullable|date_format:Y-m-d',
            'path_file_full' => 'nullable|file|mimes:pdf',
            'path_file_short' => 'nullable|file|mimes:pdf',
            'contract_status' => 'required|in:in_progress,completed',
            'intermediate_product_status' => 'required|in:completed,in_progress,pending_review,multi_year,technical_done,has_issues,issues_recorded',
            'financial_status' => 'required|in:in_progress,completed',
            'note' => 'nullable|max:255',
            'is_special' => 'required|boolean',
            'a_side' => 'required|max:255',
            'b_side' => 'required|max:255',
            'many_years' => 'nullable|array',
            'many_years.*' => 'nullable|integer',
            'scopes' => 'nullable|array',
            'scopes.*' => 'exists:provinces,code',
            'professionals' => 'nullable|array',
            'professionals.*' => 'nullable|exists:users,id',
            'disbursements' => 'nullable|array',
            'disbursements.*' => 'nullable|exists:users,id',
            'intermediate_collaborators' => 'nullable|array',
            'intermediate_collaborators.*' => 'nullable|exists:users,id',
        ];
    }
}
