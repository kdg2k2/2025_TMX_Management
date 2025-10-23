<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bidding\DeleteRequest;
use App\Http\Requests\Bidding\EditRequest;
use App\Http\Requests\Bidding\FindByIdRequest;
use App\Services\BiddingService;

class BiddingController extends Controller
{
    public function __construct()
    {
        $this->service = app(BiddingService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.bidding.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.bidding.create');
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.bidding.edit', [
                'data' => $this->service->findById($request->validated()['id'])
            ]);
        });
    }

    public function delete(DeleteRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->delete($request->validated()['id']),
                'message' => config('message.delete'),
            ]);
        });
    }

    public function show(FindByIdRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.bidding.show', $this->service->getShowBaseData($request->validated()['id']));
        });
    }
}
