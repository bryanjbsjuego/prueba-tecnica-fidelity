<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerCategory;

class CustomerCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'categoria_cliente_id' => 1,
                'nombre' => 'Categoría Oro',
                'programa_id' => 1,
            ],
            [
                'categoria_cliente_id' => 2,
                'nombre' => 'Categoría Plata',
                'programa_id' => 1,
            ],
            [
                'categoria_cliente_id' => 3,
                'nombre' => 'Categoría Bronce',
                'programa_id' => 1,
            ],
            [
                'categoria_cliente_id' => 4,
                'nombre' => 'Categoría VIP',
                'programa_id' => 2,
            ],
            [
                'categoria_cliente_id' => 3220,
                'nombre' => 'Categoría Cliente 3220',
                'programa_id' => 1,
            ],
        ];

        foreach ($categories as $category) {
            CustomerCategory::create($category);
        }
    }
}
