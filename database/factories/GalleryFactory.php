<?php

namespace Database\Factories;

use App\Enums\GalleryCategory;
use App\Models\Gallery;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Gallery>
 */
class GalleryFactory extends Factory
{
    public function definition(): array
    {
        $category = fake()->randomElement(GalleryCategory::cases());

        return [
            'title' => $category->label().' '.fake()->year(),
            'category' => $category->value,
            'description' => fake()->sentence(12),
            'created_by' => User::factory(),
        ];
    }
}
