<?php

namespace Database\Factories;

use App\Enum\EventStatusEnum;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @template TModel of \App\Models\Event
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('+1 days', '+30 days');
        $endDate = (clone $startDate)->modify('+'.rand(1, 5).' hours');

        return [
            'name' => ucfirst($this->faker->words(3, true)),
            'description' => $this->faker->optional()->paragraphs(2, true),
            'location' => $this->faker->city(),
            'capacity' => $this->faker->numberBetween(10, 100),
            'waitListCapacity' => $this->faker->numberBetween(0, 30),
            'status' => $this->faker->randomElement(array_column(EventStatusEnum::cases(), 'value')), // Uses Enum values
            'starts_at' => $startDate,
            'ends_at' => $endDate,
        ];
    }
}
