<?php

namespace App\Services;

use Exception;
use App\Models\Tax;
use App\Enums\TaxType;
use App\Http\Requests\TaxRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;

class TaxService
{
    protected array $taxFilter = [
        'name',
        'code',
        'tax_rate',
        'type',
        'status'
    ];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            return Tax::where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->taxFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
                    }
                }
            })->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(TaxRequest $request)
    {
        try {
            return Tax::create($request->validated() + ['type' => TaxType::PERCENTAGE]);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(TaxRequest $request, Tax $tax)
    {
        try {
            return tap($tax)->update($request->validated());
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Tax $tax): void
    {
        try {
            $checkItem = $tax->items->whereNull('deleted_at');
            if (!blank($checkItem)) {
                $tax->delete();
            } else {
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                $tax->delete();
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }
        } catch (Exception $exception) {
            Log::info(QueryExceptionLibrary::message($exception));
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Tax $tax): Tax
    {
        try {
            return $tax;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }
}