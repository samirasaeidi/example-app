<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Random\RandomException;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     *
     * @throws RandomException
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->name(),
            'parent_id' => null,
            'slug' => fake()->slug(),
            'active' => random_int(0, 1),
        ];

    }

    public function withChildren(int $levels = 7, int $children = 3)
    {
        return $this->afterCreating(function (Category $category) use ($levels, $children) {
            if ($levels > 1) {
                Category::factory()
                    ->count($children)
                    ->create([
                        'parent_id' => $category->id,
                    ])->each(function (Category $child) use ($levels, $children) {
                        $child->factory()->withChildren($levels - 1, $children);
                    });
            }
        });
    }
}
