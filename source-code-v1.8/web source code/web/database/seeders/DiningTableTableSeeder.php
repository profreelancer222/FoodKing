<?php

namespace Database\Seeders;

use App\Enums\Ask;
use App\Models\DiningTable;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;
use App\Models\Item;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use App\Enums\ItemType;
use App\Enums\Status;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DiningTableTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public array $tables = [
        [
            'name'      => 'Table 1',
            'slug'      => 'table-1',
            'size'      => 6,
            'branch_id' => 1
        ],
        [
            'name'      => 'Table 2',
            'slug'      => 'table-2',
            'size'      => 8,
            'branch_id' => 1
        ],
        [
            'name'      => 'Table 1',
            'slug'      => 'table-2-1',
            'size'      => 10,
            'branch_id' => 2
        ],
        [
            'name'      => 'Table 2',
            'slug'      => 'table-2-2',
            'size'      => 6,
            'branch_id' => 2
        ]
    ];

    public function run(): void
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            foreach ($this->tables as $table) {
                $filename = Str::random(10) . '.png';
                if (!File::exists(storage_path('app/public/qr_codes/'))) {
                    File::makeDirectory(storage_path('app/public/qr_codes/'));
                }
                QrCode::format('png')->size(200)->generate(URL::to('/') . "/#/menu/" . $table['slug'], storage_path('app/public/qr_codes/' . $filename));

                DiningTable::create([
                    'name'      => $table['name'],
                    'slug'      => $table['slug'],
                    'size'      => $table['size'],
                    'branch_id' => $table['branch_id'],
                    'qr_code'   => 'storage/qr_codes/' . $filename
                ]);
            }
        }
    }
}
