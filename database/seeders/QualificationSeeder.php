<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Qualification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class QualificationSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::create(2026, 6, 15);

        $programs = [
            'Преподаватель' => 'Современные методики дополнительного профессионального образования',
            'Старший преподаватель' => 'Современные методики дополнительного профессионального образования',
            'Методист' => 'Проектирование программ ДПО',
            'Специалист по охране труда' => 'Охрана труда (для специалистов)',
            'Инженер лаборатории' => 'Метрологическое обеспечение испытаний',
            'Специалист по кадрам' => 'Кадровое делопроизводство 2024',
            'Главный бухгалтер' => 'Изменения в бухгалтерском и налоговом учёте',
            'Бухгалтер' => 'Основы 1С: Бухгалтерия 8.3',
            'Системный администратор' => 'Информационная безопасность',
            'Заместитель директора по учебной работе' => 'Управление образовательной организацией',
        ];

        $employees = Employee::with('position')->get();

        $monthsAgoByIndex = [-50, -42, -36, -28, -20, -34, -12, -6, -39, -45, -33, -10, -8, -25];

        foreach ($employees->values() as $idx => $employee) {
            $programName = $programs[$employee->position->title] ?? null;

            if (! $programName) {
                continue;
            }

            $monthsAgo = $monthsAgoByIndex[$idx] ?? -24;
            $date = $today->copy()->addMonths($monthsAgo);

            Qualification::updateOrCreate(
                ['employee_id' => $employee->id, 'date' => $date->toDateString()],
                [
                    'course_name' => $programName,
                    'next_date' => $date->copy()->addYears(3)->toDateString(),
                ]
            );
        }
    }
}
