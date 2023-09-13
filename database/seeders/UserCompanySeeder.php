<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i=0; $i < 30; $i++) { 
            DB::table('user_company')->insert([
                'user_id' => rand(1, 10),
                'company_id' => rand(1, 10),
            ]);
        }
    }
}
 