<?php

namespace Database\Factories;

use App\Models\Subscribe;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscribe>
 */
class SubscribeFactory extends Factory
{

    /**
     * @return array
     */
    protected function value()
    {
        $users = User::all();
        while (true) {
            $user = $users->random()->id;
            $subscriber = $users->random()->id;
            var_dump(!Subscribe::where([
                'user_id' => $user,
                'subscriber_id' => $subscriber
            ])->count());
            if ($user !== $subscriber && !Subscribe::where([
                    'user_id' => $user,
                    'subscriber_id' => $subscriber
                ])->count())  {
                return [
                    'user_id' => $user,
                    'subscriber_id' => $subscriber,
                ];
            }
        }
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return $this->value();
    }
}
