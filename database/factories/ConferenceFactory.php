<?php

namespace Database\Factories;

use App\Models\Conference;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Conference>
 */
class ConferenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
        protected $model = Conference::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create();
        return [
            'title' => $faker->sentence,
            'conference_date' => $faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'description' => $faker->paragraph,
            'key_note_speaker' => $faker->name,
            'invited_speaker' => $faker->name,
            'topics' => $faker->paragraph,
            'general_chair' => $faker->name,
            'co_chair' => $faker->name,
            'program_chair' => $faker->name,
            'paper_sub_guide' => $faker->sentence,
            'sub_deadline' => $faker->dateTimeBetween('-1 month', '+6 months')->format('Y-m-d'),
            'updated_sub_deadline' => $faker->dateTimeBetween('-1 month', '+6 months')->format('Y-m-d'),
            'accept_noti' => $faker->word,
            'normal_fee' => $faker->randomFloat(2, 50, 500),
            'early_bird_fee' => $faker->randomFloat(2, 30, 400),
            'local_fee' => $faker->randomFloat(2, 20, 300),
            'sub_email' => $faker->email,
            'camera_ready' => $faker->word,
            'brochure' => $faker->word,
            'book' => $faker->word,
        ];
    }
}
