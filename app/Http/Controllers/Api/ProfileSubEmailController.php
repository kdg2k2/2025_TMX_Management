<?php

namespace App\Http\Controllers\Api;

use App\Services\ProfileSubEmailService;

class ProfileSubEmailController extends UserSubEmailController
{
    public function __construct()
    {
        $this->service = app(ProfileSubEmailService::class);
    }
}
