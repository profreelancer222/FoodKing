<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Enums\Activity;
use App\Enums\InputType;
use App\Models\SmsGateway;
use App\Models\GatewayOption;
use Illuminate\Database\Seeder;

class SmsGatewayTableSeederVersionTwo extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public array $gateways = [
        [
            "name"    => "2Factor",
            "slug"    => "twofactor",
            "misc"    => null,
            "status"  => Activity::DISABLE,
            "options" => [
                [
                    "option"     => 'twofactor_module',
                    "value"      => 'PROMO_SMS',
                    "type"       => InputType::TEXT,
                    "activities" => '',
                ],
                [
                    "option"     => 'twofactor_from',
                    "type"       => InputType::TEXT,
                    "activities" => '',
                ],
                [
                    "option"     => 'twofactor_api_key',
                    "type"       => InputType::TEXT,
                    "activities" => '',
                ],
                [
                    "option"     => 'twofactor_status',
                    "value"      => Activity::DISABLE,
                    "type"       => InputType::SELECT,
                    "activities" => [
                        Activity::ENABLE  => "enable",
                        Activity::DISABLE => "disable",
                    ]
                ]
            ]
        ],
        [
            "name"    => "Bulksms",
            "slug"    => "bulksms",
            "misc"    => null,
            "status"  => Activity::DISABLE,
            "options" => [
                [
                    "option"     => 'bulksms_username',
                    "type"       => InputType::TEXT,
                    "activities" => '',
                ],
                [
                    "option"     => 'bulksms_password',
                    "type"       => InputType::TEXT,
                    "activities" => '',
                ],
                [
                    "option"     => 'bulksms_status',
                    "value"      => Activity::DISABLE,
                    "type"       => InputType::SELECT,
                    "activities" => [
                        Activity::ENABLE  => "enable",
                        Activity::DISABLE => "disable",
                    ]
                ]
            ]
        ],
        [
            "name"    => "Bulksmsbd",
            "slug"    => "bulksmsbd",
            "misc"    => null,
            "status"  => Activity::DISABLE,
            "options" => [
                [
                    "option"     => 'bulksmsbd_api_key',
                    "type"       => InputType::TEXT,
                    "activities" => '',
                ],
                [
                    "option"     => 'bulksmsbd_sender_id',
                    "type"       => InputType::TEXT,
                    "activities" => '',
                ],
                [
                    "option"     => 'bulksmsbd_status',
                    "value"      => Activity::DISABLE,
                    "type"       => InputType::SELECT,
                    "activities" => [
                        Activity::ENABLE  => "enable",
                        Activity::DISABLE => "disable",
                    ]
                ]
            ]
        ],
        [
            "name"    => "Telesign",
            "slug"    => "telesign",
            "misc"    => null,
            "status"  => Activity::DISABLE,
            "options" => [
                [
                    "option"     => 'telesign_api_key',
                    "type"       => InputType::TEXT,
                    "activities" => '',
                ],
                [
                    "option"     => 'telesign_customer_id',
                    "type"       => InputType::TEXT,
                    "activities" => '',
                ],
                [
                    "option"     => 'telesign_sender_id',
                    "type"       => InputType::TEXT,
                    "activities" => '',
                ],
                [
                    "option"     => 'telesign_status',
                    "value"      => Activity::DISABLE,
                    "type"       => InputType::SELECT,
                    "activities" => [
                        Activity::ENABLE  => "enable",
                        Activity::DISABLE => "disable",
                    ]
                ]
            ]
        ],
    ];

    public function run(): void
    {
        foreach ($this->gateways as $gateway) {
            $sms = SmsGateway::create([
                'name'   => $gateway['name'],
                'slug'   => $gateway['slug'],
                'misc'   => json_encode($gateway['misc']),
                'status' => Status::ACTIVE,
            ]);
            $this->gatewayOption($sms->id, $gateway['options']);
        }
    }

    public function gatewayOption($id, $options): void
    {
        foreach ($options as $option) {
            GatewayOption::create([
                'model_id'   => $id,
                'model_type' => 'App\Models\SmsGateway',
                'option'     => $option['option'],
                'value'      => $option['value'] ?? "",
                'type'       => $option['type'],
                'activities' => json_encode($option['activities'])
            ]);
        }
    }
}
