<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_types')->insert([
            [
                'name' => 'Cash',
                'system_name' => 'cash',
                'description' => 'Pay via Cash',
                'is_online' => 0,
                'is_system' => 1,
                'active' => 1,
                'is_cash' => 1,
                'options' => json_encode([
                ]),
            ],
        ]);
    }
}
