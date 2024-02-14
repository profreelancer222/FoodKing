<?php

namespace App\Http\Controllers\Admin;


use Exception;
use App\Models\DiningTable;
use App\Exports\DiningTableExport;
use App\Services\DiningTableService;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\DiningTableRequest;
use App\Http\Resources\DiningTableResource;

class DiningTableController extends AdminController
{
    private DiningTableService $diningTableService;

    public function __construct(DiningTableService $diningTable)
    {
        parent::__construct();
        $this->diningTableService = $diningTable;
        $this->middleware(['permission:dining-tables'])->only('export');
        $this->middleware(['permission:dining_tables_create'])->only('store');
        $this->middleware(['permission:dining_tables_edit'])->only('update');
        $this->middleware(['permission:dining_tables_delete'])->only('destroy');
        $this->middleware(['permission:dining_tables_show'])->only('show');
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return DiningTableResource::collection($this->diningTableService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function store(
        DiningTableRequest $request
    ): \Illuminate\Http\Response | DiningTableResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new DiningTableResource($this->diningTableService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(
        DiningTable $diningTable
    ): \Illuminate\Http\Response | DiningTableResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new DiningTableResource($this->diningTableService->show($diningTable));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(
        DiningTableRequest $request,
        DiningTable $diningTable
    ): \Illuminate\Http\Response | DiningTableResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new DiningTableResource($this->diningTableService->update($request, $diningTable));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(
        DiningTable $diningTable
    ): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            $this->diningTableService->destroy($diningTable);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response | \Symfony\Component\HttpFoundation\BinaryFileResponse | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new DiningTableExport($this->diningTableService, $request), 'Dining-Table.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}