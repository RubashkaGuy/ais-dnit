<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            ['name' => 'Охрана труда (руководители и специалисты)', 'hours' => 40, 'price' => 4000],
            ['name' => 'Промышленная безопасность', 'hours' => 72, 'price' => 8000],
            ['name' => 'Пожарная безопасность (ПТМ)', 'hours' => 16, 'price' => 3000],
            ['name' => 'Работы на высоте (1-3 группа)', 'hours' => 24, 'price' => 6500],
            ['name' => 'Перевозка опасных грузов (ДОПОГ)', 'hours' => 24, 'price' => 7000],
            ['name' => 'Экологическая безопасность', 'hours' => 72, 'price' => 9000],
            ['name' => 'Электробезопасность (II-V группа)', 'hours' => 40, 'price' => 5500],
            ['name' => 'Энергоаудит', 'hours' => 72, 'price' => 12000],
            ['name' => 'Специальная оценка условий труда', 'hours' => 72, 'price' => 11000],
            ['name' => 'Профессиональная переподготовка «Охрана труда»', 'hours' => 256, 'price' => 18000],
            ['name' => 'Первая помощь пострадавшим', 'hours' => 16, 'price' => 2500],
        ];

        foreach ($courses as $course) {
            Course::updateOrCreate(['name' => $course['name']], $course);
        }
    }
}
