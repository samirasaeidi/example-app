<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::factory()
            ->count(10)
            ->withChildren()
            ->create();

        Category::factory()
            ->count(10)
            ->withChildren(3,1)
            ->create();

        Category::factory()
            ->count(5)
            ->withChildren(1,0)
            ->create();

        Category::factory()
            ->count(10)
            ->create();
    }
}
