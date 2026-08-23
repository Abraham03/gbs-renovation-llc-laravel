<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category; // Importamos el modelo

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['id' => 1, 'name' => 'Deck, Siding & Windows'],
            ['id' => 2, 'name' => 'Kitchen & Bathroom'],
            ['id' => 3, 'name' => 'Painting & Finishing'],
            ['id' => 4, 'name' => 'Repairs & Installation'],
        ];

        // Insertamos o actualizamos para evitar duplicados si corres el comando dos veces
        foreach ($categories as $category) {
            Category::updateOrCreate(['id' => $category['id']], ['name' => $category['name']]);
        }
    }
}