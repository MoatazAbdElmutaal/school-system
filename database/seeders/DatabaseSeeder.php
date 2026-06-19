<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Payment;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء الفصول الخمسة
        $classrooms = collect([
            ['class_name' => 'الأول الابتدائي', 'annual_fees' => 150000],
            ['class_name' => 'الثاني الابتدائي', 'annual_fees' => 180000],
            ['class_name' => 'الثالث الابتدائي', 'annual_fees' => 200000],
            ['class_name' => 'الرابع الابتدائي', 'annual_fees' => 220000],
            ['class_name' => 'الخامس الابتدائي', 'annual_fees' => 250000],
        ])->map(function ($data) {
            return Classroom::create($data);
        });

        // 2. إنشاء 500 طالب وتوزيعهم
        // استخدمنا factory(500) لإنتاج عدد كبير دفعة واحدة
        Student::factory()->count(500)->create([
            'classroom_id' => fn() => $classrooms->random()->id
        ])->each(function ($student) {
            
            // 3. إضافة دفعات مالية لـ 60% من الطلاب عشوائياً
            if (rand(1, 10) <= 6) {
                Payment::factory()->create([
                    'student_id' => $student->id,
                    // المبلغ المحصل يكون عشوائي بين 20 ألف ورسوم الفصل كاملة
                    'amount_paid' => rand(20000, $student->classroom->annual_fees)
                ]);
            }
        });

        $this->command->info('تمت العملية بنجاح! 5 فصول و 500 طالب جاهزون.');
    }
}