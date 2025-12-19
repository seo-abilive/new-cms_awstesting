<?php
namespace App\Mod\ContactSetting\Database\Factories;

use App\Mod\ContactSetting\Domain\Models\ContactSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactSettingFactory extends Factory
{
    protected $model = ContactSetting::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word
        ];
    }
}
