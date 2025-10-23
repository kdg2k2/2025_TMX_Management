<?php

namespace App\Http\Requests\BinddingSoftwareOwnership;

class DeleteBySoftwareOwnershipIdRequest extends FindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:bindding_software_ownerships,software_ownership_id',
        ];
    }
}
