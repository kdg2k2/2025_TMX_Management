<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ContractProductMinuteSignatureController extends Controller
{
    public function index()
    {
        return $this->catchWeb(fn() => view('admin.pages.contract.product.minute.sign.index'));
    }
}
