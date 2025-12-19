<?php
namespace App\Core\Contract\Database\Factories;

use App\Core\Contract\Domain\Models\Contract;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContractFactory extends Factory
{
    protected $model = Contract::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word
        ];
    }
}
