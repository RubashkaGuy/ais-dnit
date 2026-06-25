<?php

namespace Database\Seeders;

use App\Enums\ContractStatus;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ContractSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all();
        $courses = Course::all();

        if ($clients->isEmpty() || $courses->isEmpty()) {
            return;
        }

        $today = Carbon::create(2026, 6, 15);
        $statuses = [ContractStatus::Paid, ContractStatus::Studying, ContractStatus::Completed, ContractStatus::Pending];

        $contracts = [
            ['ДНиТ/2026-001', -120, 'Охрана труда (руководители и специалисты)', 0, ContractStatus::Completed],
            ['ДНиТ/2026-002', -90, 'Промышленная безопасность', 1, ContractStatus::Completed],
            ['ДНиТ/2026-003', -75, 'Работы на высоте (1-3 группа)', 2, ContractStatus::Paid],
            ['ДНиТ/2026-004', -60, 'Электробезопасность (II-V группа)', 3, ContractStatus::Studying],
            ['ДНиТ/2026-005', -45, 'Специальная оценка условий труда', 4, ContractStatus::Studying],
            ['ДНиТ/2026-006', -30, 'Пожарная безопасность (ПТМ)', 5, ContractStatus::Paid],
            ['ДНиТ/2026-007', -25, 'Экологическая безопасность', 6, ContractStatus::Pending],
            ['ДНиТ/2026-008', -20, 'Перевозка опасных грузов (ДОПОГ)', 7, ContractStatus::Pending],
            ['ДНиТ/2026-009', -15, 'Профессиональная переподготовка «Охрана труда»', 8, ContractStatus::Studying],
            ['ДНиТ/2026-010', -10, 'Первая помощь пострадавшим', 9, ContractStatus::Paid],
            ['ДНиТ/2026-011', -8, 'Охрана труда (руководители и специалисты)', 10, ContractStatus::Pending],
            ['ДНиТ/2026-012', -5, 'Энергоаудит', 11, ContractStatus::Pending],
            ['ДНиТ/2026-013', -3, 'Электробезопасность (II-V группа)', 12, ContractStatus::Pending],
            ['ДНиТ/2026-014', -2, 'Работы на высоте (1-3 группа)', 0, ContractStatus::Pending],
            ['ДНиТ/2026-015', -1, 'Промышленная безопасность', 1, ContractStatus::Pending],
        ];

        foreach ($contracts as [$number, $daysAgo, $courseName, $clientIndex, $status]) {
            $client = $clients[$clientIndex % $clients->count()];
            $course = $courses->firstWhere('name', $courseName) ?? $courses->random();

            Contract::updateOrCreate(
                ['number' => $number],
                [
                    'client_id' => $client->id,
                    'course_id' => $course->id,
                    'date' => $today->copy()->addDays($daysAgo)->toDateString(),
                    'amount' => $course->price,
                    'status' => $status,
                ]
            );
        }
    }
}
