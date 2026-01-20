<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ContractProductService;

class ContractProductController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractProductService::class);
    }

    public function index()
    {
        return $this->catchWeb(fn() => view('admin.pages.contract.product.index', $this->service->getBaseDataForLView()));
    }
}
