<?php

namespace Database\Factories;

use App\Enums\DocumentCategory;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    public function definition(): array
    {
        $category = fake()->randomElement(DocumentCategory::cases());

        return [
            'title' => $category->label().' — '.fake()->year(),
            'category' => $category->value,
            'description' => fake()->sentence(10),
            'file_path' => 'documents/placeholder.pdf',
            'file_size' => fake()->numberBetween(20_000, 2_000_000),
            'uploaded_by' => User::factory(),
        ];
    }
}
