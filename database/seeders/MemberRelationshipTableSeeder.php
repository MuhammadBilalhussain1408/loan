<?php

namespace Database\Seeders;

use App\Models\MemberRelationship;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemberRelationshipTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('member_relationships')->insert([
            [
                'name' => 'Spouse'
            ],
            [
                'name' => 'Child'
            ],
        ]);
    }
}
