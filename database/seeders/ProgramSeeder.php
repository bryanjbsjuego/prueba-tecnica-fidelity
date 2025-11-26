<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            ['nombre' => 'Programa Fidely Principal', 'estatus' => true],
            ['nombre' => 'Programa Fidely Secundario', 'estatus' => true],
            ['nombre' => 'Programa Test', 'estatus' => false],
        ];

        foreach ($programs as $program) {
            Program::create($program);
        }
    }
}
