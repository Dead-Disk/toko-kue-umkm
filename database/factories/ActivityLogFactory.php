<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $role = fake()->randomElement(['admin', 'staff']);
        $action = fake()->randomElement(['login', 'logout', 'tambah', 'ubah', 'hapus', 'kasir']);

        return [
            'user_id' => User::factory()->state(['role' => $role]),
            'user_name' => fake()->name(),
            'user_role' => $role,
            'action' => $action,
            'description' => fake()->sentence(),
            'subject_type' => 'Product',
            'subject_id' => (string) fake()->randomDigitNotNull(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'properties' => null,
        ];
    }

    public function forStaff(): static
    {
        return $this->state(fn () => [
            'user_role' => 'staff',
        ]);
    }

    public function forAdmin(): static
    {
        return $this->state(fn () => [
            'user_role' => 'admin',
        ]);
    }
}
