<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Учебный отдел', 'head' => 'Кузнецова И. С.'],
            ['name' => 'Отдел охраны труда', 'head' => 'Соколов А. М.'],
            ['name' => 'Аккредитованная лаборатория', 'head' => 'Морозов В. Н.'],
            ['name' => 'Бухгалтерия', 'head' => 'Лебедева Т. А.'],
            ['name' => 'Кадровая служба', 'head' => 'Никифорова О. В.'],
            ['name' => 'Административно-хозяйственный отдел', 'head' => 'Григорьев Р. С.'],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(['name' => $department['name']], $department);
        }
    }
}
