<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Operator;
use Illuminate\Support\Facades\Hash;

class OperatorSeeder extends Seeder
{
    public function run(): void
    {
        $operators = [
            [
                'usuario' => 'operador1',
                'contrasena' => Hash::make('Password123'),
                'estatus' => true,
                'programa_id' => 1,
            ],
            [
                'usuario' => 'operador2',
                'contrasena' => Hash::make('Password123'),
                'estatus' => true,
                'programa_id' => 1,
            ],
            [
                'usuario' => 'admin',
                'contrasena' => Hash::make('Admin123'),
                'estatus' => true,
                'programa_id' => 2,
            ],
        ];

        foreach ($operators as $operator) {
            Operator::create($operator);
        }
    }
}
