<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Video>
 */
class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->sentence,
            'public' => $this->faker->boolean(),
            'user_id' => User::all()->random()->id,
            'category_id' => Category::all()->random()->id,
            'video' => 'uploads/playlist/phpg9ephs5on0528va6AMb/index.m3u8',
            'thumbnail' => 'previews/7ZxVxSPf28LLAOGwb65wneVWpVVBThJKF66cvlpT.jpg',
            'duration' => 49667
        ];
    }
}
