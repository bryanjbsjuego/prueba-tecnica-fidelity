<?php

namespace Database\Seeders;

use App\Models\Alliance;
use App\Models\CustomerCategory;
use App\Models\Operator;
use App\Models\Program;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            ProgramSeeder::class,
            OperatorSeeder::class,
            CustomerCategorySeeder::class,
            AllianceSeeder::class,
        ]);


    }
}
