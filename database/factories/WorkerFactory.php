<?php

namespace Database\Factories;

use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Worker>
 */
class WorkerFactory extends Factory
{
    protected static ?string $password;

    protected $model = Worker::class;

    public function definition(): array
    {
        $name = fake()->name();

        return [
            'name' => $name,
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'phone' => fake()->phoneNumber(),
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('##'),
            'title' => fake()->jobTitle(),
            'bio' => fake()->optional()->sentence(),
            'is_active' => true,
            'sort_order' => 0,
            'slot_duration_minutes' => 60,
            'google_calendar_enabled' => false,
        ];
    }
}
