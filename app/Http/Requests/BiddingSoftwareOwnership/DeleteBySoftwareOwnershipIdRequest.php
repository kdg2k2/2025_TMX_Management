<?php

namespace App\Http\Requests\BiddingSoftwareOwnership;

class DeleteBySoftwareOwnershipIdRequest extends FindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:bidding_software_ownerships,software_ownership_id',
        ];
    }
}
