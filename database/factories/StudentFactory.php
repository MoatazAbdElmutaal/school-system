<?php

namespace Database\Factories;
use App\Models\Classroom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $addresses = ['الخرطوم، الرياض', 'أم درمان، الثورة', 'بحري، كافوري', 'الدبة، حي الشاطئ', 'عطبرة، المطار'];
       return [
        'full_name'   => $this->faker->name(),
        'student_phone'       => '09' . $this->faker->numerify('########'),
        'national_id' => $this->faker->unique()->numerify('###########'),
        'classroom_id' => Classroom::first()?->id ?? Classroom::factory(),
        'registration_number' => 'REG-' . $this->faker->unique()->numberBetween(1000, 9000), // إضافة الحقل الناقص
        'guardian_name'       =>$this->faker->name(), // أضفنا الحقل الناقص هنا
        'guardian_phone'      => '09' . rand(11111111, 99999999),
        'address'             => collect($addresses)->random(), // اختيار عنوان منطقي عشوائي
       'date_of_birth'       => now()->subYears(rand(6, 15))->format('Y-m-d'),

    ];
    }
}
