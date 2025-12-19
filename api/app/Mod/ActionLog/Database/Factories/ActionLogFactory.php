<?php
namespace App\Mod\ActionLog\Database\Factories;

use App\Mod\ActionLog\Domain\Models\ActionLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActionLogFactory extends Factory
{
    protected $model = ActionLog::class;

    public function definition(): array
    {
        return [
            'ip' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
            'path' => $this->faker->filePath(),
            'method' => $this->faker->randomElement(['GET', 'POST', 'PUT', 'DELETE']),
            'params' => ['key' => $this->faker->word],
            'http_status' => $this->faker->randomElement([200, 201, 400, 404, 500]),
            'duration' => $this->faker->randomFloat(4, 0, 2),
        ];
    }
}
