<?php

namespace App\Services;


use App\Http\Requests\DiningTableRequest;
use App\Http\Requests\PaginateRequest;
use App\Models\DiningTable;
use Exception;
use Illuminate\Support\Facades\Log;
use Smartisan\Settings\Facades\Settings;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;

class DiningTableService
{
    protected array $diningTableFilter = [
        'name',
        'size',
        'branch_id',
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

            return DiningTable::with('branch')->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->diningTableFilter)) {
                        if ($key == "except") {
                            $explodes = explode('|', $request);
                            if (count($explodes)) {
                                foreach ($explodes as $explode) {
                                    $query->where('id', '!=', $explode);
                                }
                            }
                        } else {
                            if ($key == "branch_id") {
                                $query->where($key, $request);
                            } else {
                                $query->where($key, 'like', '%' . $request . '%');
                            }
                        }
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
    public function store(DiningTableRequest $request)
    {
        try {
            $filename = Str::random(10) . '.png';
            $slug     = Str::slug($request->name);
            $url      = URL::to('/') . "/#/menu/" . $slug;

            if (!File::exists(storage_path('app/public/qr_codes/'))) {
                File::makeDirectory(storage_path('app/public/qr_codes/'));
            }
            QrCode::format('png')->size(200)->generate($url, storage_path('app/public/qr_codes/' . $filename));
            return DiningTable::create($request->validated() + ['qr_code' => 'storage/qr_codes/' . $filename, 'slug' => $slug]);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(DiningTableRequest $request, DiningTable $diningTable)
    {
        try {
            $filename = Str::random(10) . '.png';
            $slug     = Str::slug($request->name);
            $url      = URL::to('/') . "/#/menu/" . $slug;

            if (!File::exists(storage_path('app/public/qr_codes/'))) {
                File::makeDirectory(storage_path('app/public/qr_codes/'));
            }

            if(File::exists($diningTable->qr_code)){
                File::delete($diningTable->qr_code);
            }

            QrCode::format('png')->size(200)->generate($url, storage_path('app/public/qr_codes/' . $filename));

            return tap($diningTable)->update($request->validated() + ['qr_code' => 'storage/qr_codes/' . $filename, 'slug' => $slug]);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(DiningTable $diningTable): void
    {
        try {
            if(File::exists($diningTable->qr_code)){
                File::delete($diningTable->qr_code);
            }
            $diningTable->delete();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(DiningTable $diningTable): DiningTable
    {
        try {
            return $diningTable;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }
}
