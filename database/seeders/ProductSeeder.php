<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Team;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $team = Team::first();

        // 3 Kategori oluştur
        $category1 = ProductCategory::create([
            'team_id' => $team->id,
            'name' => 'Ana Yemek',
            'description' => 'Ana yemek kategorisi',
            'status' => '1',
        ]);

        $category2 = ProductCategory::create([
            'team_id' => $team->id,
            'name' => 'İçecekler',
            'description' => 'İçecek kategorisi',
            'status' => '1',
        ]);

        $category3 = ProductCategory::create([
            'team_id' => $team->id,
            'name' => 'Tatlılar',
            'description' => 'Tatlı kategorisi',
            'status' => '1',
        ]);

        // 4 Ürün oluştur
        Product::create([
            'team_id' => $team->id,
            'name' => 'Hamburger',
            'description' => 'Klasik hamburger',
            'product_category_id' => $category1->id,
            'price' => '150.00',
            'status' => '1',
        ]);

        Product::create([
            'team_id' => $team->id,
            'name' => 'Pizza',
            'description' => 'Karışık pizza',
            'product_category_id' => $category1->id,
            'price' => '200.00',
            'status' => '1',
        ]);

        Product::create([
            'team_id' => $team->id,
            'name' => 'Kola',
            'description' => 'Soğuk kola',
            'product_category_id' => $category2->id,
            'price' => '25.00',
            'status' => '1',
        ]);

        Product::create([
            'team_id' => $team->id,
            'name' => 'Baklava',
            'description' => 'Antep fıstıklı baklava',
            'product_category_id' => $category3->id,
            'price' => '80.00',
            'status' => '1',
        ]);
    }
}

