<?php
namespace App\Mod\ContentField\Database\Factories;

use App\Mod\ContentField\Domain\Models\ContentField;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContentFieldFactory extends Factory
{
    protected $model = ContentField::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'field_id' => $this->faker->unique()->word,
            'is_required' => $this->faker->boolean,
            'is_list_heading' => $this->faker->boolean,
            'field_type' => $this->faker->randomElement(['text', 'textarea', 'media_image', 'media_file']),
        ];
    }
}
