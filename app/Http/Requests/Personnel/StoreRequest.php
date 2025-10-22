<?php

namespace App\Http\Requests\Personnel;

use App\Http\Requests\BaseRequest;
use Exception;

class StoreRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'created_by' => $this->user()->id,
        ]);
    }

    public function rules(): array
    {
        $rules = [
            'created_by' => 'required|exists:users,id',
            'name' => 'required|max:255|unique:personnels,name',
            'personnel_unit_id' => 'required|exists:personnel_units,id',
            'educational_level' => 'required|max:255',
        ];

        $fields = app(\App\Services\PersonnelCustomFieldService::class)->list([
            'load_relations' => false,
        ]);
        foreach ($fields as $field) {
            $rules[$field['field']] = ['nullable'];
            switch ($field['type']['original']) {
                case 'date':
                    $rules[$field['field']][] = 'date_format:Y-m-d';
                    break;
                case 'datetime-local':
                    $rules[$field['field']][] = 'date_format:Y-m-d\TH:i';
                    break;
                case 'text':
                    $rules[$field['field']][] = 'max:255';
                    break;
                case 'number':
                    $rules[$field['field']][] = 'min:0';
                    break;

                default:
                    throw new Exception('Không xác định được định dạng');
            }
        }

        return $rules;
    }
}
