<?php

namespace Database\Factories;

use App\Enums\NotificationChannelEnum;
use App\Enums\NotificationTypeEnum;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'team_id' => Team::factory(),
            'type' => NotificationTypeEnum::EMPTY,
            'channel' => NotificationChannelEnum::EMAIL,
            'receiver' => $this->faker->email,
            'message' => $this->faker->text,
        ];
    }
}
