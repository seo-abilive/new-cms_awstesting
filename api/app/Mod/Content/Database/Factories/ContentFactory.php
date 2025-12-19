<?php
namespace App\Mod\Content\Database\Factories;

use App\Mod\Content\Domain\Models\Content;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContentFactory extends Factory
{
    protected $model = Content::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word
        ];
    }
}
