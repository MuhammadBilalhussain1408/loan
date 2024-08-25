<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SmsGatewaysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sms_gateways')->insert([
            [
                'name' => 'Bluedot',
                'system_name' => 'bluedot',
                'is_system' => 1,
                'http_api' => 1,
                'options' => json_encode([
                    'api_id' => '',
                    'api_password' => '',
                    'sender_id' => '',
                ]),
            ],
            [
                'name' => 'Twilio',
                'system_name' => 'twilio',
                'is_system' => 1,
                'http_api' => 0,
                'options' => json_encode([
                    'account_sid' => '',
                    'auth_token' => '',
                    'from' => '',
                ]),

            ],
            [
                'name' => 'Route Mobile',
                'system_name' => 'routemobile',
                'is_system' => 1,
                'http_api' => 1,
                'options' => json_encode([
                ]),
            ],

        ]);
    }
}
