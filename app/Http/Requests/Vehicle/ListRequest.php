<?php

namespace App\Http\Requests\Vehicle;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'status' => 'nullable|in:ready,unwashed,broken,faulty,lost,loaned',
            ]
        );
    }
}
