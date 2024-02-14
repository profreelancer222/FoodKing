<?php

namespace App\Http\Controllers\Table;


use App\Http\Controllers\Controller;
use App\Models\FrontendDiningTable;
use Exception;
use App\Services\DiningTableService;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\DiningTableResource;
use App\Models\DiningTable;

class DiningTableController extends Controller
{
    private DiningTableService $diningTableService;

    public function __construct(DiningTableService $diningTable)
    {
        $this->diningTableService = $diningTable;
    }

    public function index(PaginateRequest $request) : \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return DiningTableResource::collection($this->diningTableService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(FrontendDiningTable $frontendDiningTable): DiningTableResource|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new DiningTableResource($frontendDiningTable);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
