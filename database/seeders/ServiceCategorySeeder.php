<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceCategory;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Elektrik Tesisatı', 'description' => 'Elektrik arıza, bakım ve onarım işleri.'],
            ['name' => 'Su Tesisatı', 'description' => 'Su kaçağı, boru değişimi ve sıhhi tesisat işleri.'],
            ['name' => 'Temizlik', 'description' => 'Ev ve ofis temizlik hizmetleri.'],
            ['name' => 'Boya & Badana', 'description' => 'İç ve dış cephe boya işleri.'],
            ['name' => 'Marangoz', 'description' => 'Mobilya montajı ve ahşap işleri.']
        ];

        foreach ($categories as $category) {
            ServiceCategory::create($category);
        }
    }
}