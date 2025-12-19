<?php
namespace App\Core\User\Database\Factories;

use App\Core\User\Domain\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word
        ];
    }
}
