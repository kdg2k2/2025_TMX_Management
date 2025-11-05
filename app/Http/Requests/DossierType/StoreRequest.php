<?php

namespace App\Http\Requests\DossierType;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'created_by' => $this->user()->id,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:dossier_types,name',
            'unit' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'quantity_limit' => 'required|integer|min:0',
            'created_by' => 'required|integer|exists:users,id',
        ];
    }
}
