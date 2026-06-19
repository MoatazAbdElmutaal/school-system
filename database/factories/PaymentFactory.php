<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
  public function definition(): array
{
    return [
        'student_id' => \App\Models\Student::factory(), // ينشئ طالب جديد لو ممررتش له ID
        'amount_paid' => fake()->randomElement([5000, 10000, 15000, 20000]), // مبالغ عشوائية
        'payment_date' => fake()->dateTimeBetween('-1 month', 'now'), // تاريخ في آخر شهر
        'receipt_number' => 'REC-' . now()->timestamp . '-' . fake()->unique()->numberBetween(100, 9999),
    ];
}
}
