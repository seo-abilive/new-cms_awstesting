<?php
namespace App\Mod\MediaLibrary\Database\Factories;

use App\Mod\MediaLibrary\Domain\Models\MediaLibrary;
use Illuminate\Database\Eloquent\Factories\Factory;

class MediaLibraryFactory extends Factory
{
    protected $model = MediaLibrary::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word
        ];
    }
}
