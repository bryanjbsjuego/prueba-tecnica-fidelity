<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alliance;

class AllianceSeeder extends Seeder
{
    public function run(): void
    {
        $alliances = [
            [
                'nombre' => '20% de descuento en restaurante La Delicia',
                'descripcion' => 'Disfruta de un 20% de descuento en todos los platillos de nuestro menú',
                'estatus' => 'activo'
            ],
            [
                'nombre' => '2x1 en entradas de cine',
                'descripcion' => 'Compra una entrada y lleva otra gratis en Cinépolis',
                'estatus' => 'activo',
            ],
            [
                'nombre' => '15% descuento en gimnasio FitClub',
                'descripcion' => 'Obtén 15% de descuento en tu membresía mensual o anual',
                'estatus' => 'activo',
            ],
            [
                'nombre' => 'Spa Day - 30% descuento',
                'descripcion' => 'Relájate con un día de spa con 30% de descuento en todos los tratamientos',
                'estatus' => 'activo',
            ],
            [
                'nombre' => 'Coffee Break - Café gratis',
                'descripcion' => 'Café mediano gratis en tu próxima visita a Starbucks',
                'estatus' => 'activo',
            ],
            [
                'nombre' => '10% en tienda de electrónicos',
                'descripcion' => 'Descuento especial en toda la tienda de Best Buy',
                'estatus' => 'activo',
            ],
            [
                'nombre' => 'Tour turístico - 25% off',
                'descripcion' => 'Descuento en tours turísticos de la ciudad',
                'estatus' => 'activo',
            ],
            [
                'nombre' => 'Buffet libre - Precio especial',
                'descripcion' => 'Buffet libre en restaurante El Fogón a precio especial',
                'estatus' => 'activo',
            ],
            [
                'nombre' => 'Car Wash Premium',
                'descripcion' => 'Lavado premium de auto con 40% de descuento',
                'estatus' => 'activo',
            ],
            [
                'nombre' => 'Clase de yoga gratis',
                'descripcion' => 'Primera clase gratis en Yoga Studio',
                'estatus' => 'activo',
            ],
            [
                'nombre' => 'Librería - 20% descuento',
                'descripcion' => 'Descuento en libros y material educativo',
                'estatus' => 'activo',
            ],
            [
                'nombre' => 'Masaje relajante - Oferta especial',
                'descripcion' => 'Masaje de 60 minutos con precio preferencial',
                'estatus' => 'activo',
            ],
        ];

        foreach ($alliances as $allianceData) {
            $alliance = Alliance::create($allianceData);

            // Asignar a categorías aleatoriamente
            $categories = [1, 2, 3, 4, 3220];
            shuffle($categories);
            $numCategories = rand(1, 4);

            $alliance->customerCategories()->attach(
                array_slice($categories, 0, $numCategories)
            );
        }
    }
}
