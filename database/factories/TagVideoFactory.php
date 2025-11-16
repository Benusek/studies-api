<?php

namespace Database\Factories;

use App\Models\Tag;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TagVideo>
 */
class TagVideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tag_id' => Tag::all()->random()->id,
            'video_id' => Video::all()->random()->id,
        ];
    }
}
