<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'role' => $this->faker->randomElement(['admin', 'operator']),
            'avatar' => null,
            'identification' => $this->faker->unique()->numerify('########'),
            'username' => $this->faker->unique()->userName(),
            'birth_date' => $this->faker->date(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'neighborhood' => $this->faker->word(),
            'entry_date' => $this->faker->date(),
        ];
    }

    /**
     * Indicate that the user is a superadmin.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function superadmin()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'super_admin',
                'email' => 'superadmin@example.com',
                'password' => bcrypt('password'), // You should change this in production
            ];
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
