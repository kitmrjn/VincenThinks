<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->word;
        return [
            'name' => ucfirst($name),
            'acronym' => strtoupper(substr($name, 0, 3)),
            'slug' => Str::slug($name),
        ];
    }
}