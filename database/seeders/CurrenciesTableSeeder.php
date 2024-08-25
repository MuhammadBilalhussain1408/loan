<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('currencies')->truncate();
        DB::table('currencies')->insert([
            [
                'code'=>'SZL',
                'name'=>'Swazi lilangeni',
                'symbol'=>'E',
                'xrate'=>'1',
                'active'=>'1',
            ]
        ]);
    }
}
