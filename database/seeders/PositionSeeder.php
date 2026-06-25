<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            ['title' => 'Генеральный директор', 'edu_level' => 'Высшее', 'requirements' => 'Опыт руководства, профильное образование'],
            ['title' => 'Заместитель директора по учебной работе', 'edu_level' => 'Высшее педагогическое', 'requirements' => 'Опыт работы в ДПО от 5 лет'],
            ['title' => 'Преподаватель', 'edu_level' => 'Высшее', 'requirements' => 'Опыт преподавания, регулярное повышение квалификации (раз в 3 года)'],
            ['title' => 'Старший преподаватель', 'edu_level' => 'Высшее', 'requirements' => 'Учёная степень, опыт преподавания от 5 лет'],
            ['title' => 'Методист', 'edu_level' => 'Высшее педагогическое', 'requirements' => 'Знание методики ДПО'],
            ['title' => 'Специалист по охране труда', 'edu_level' => 'Высшее техническое', 'requirements' => 'Удостоверение по ОТ, опыт от 3 лет'],
            ['title' => 'Инженер лаборатории', 'edu_level' => 'Высшее техническое', 'requirements' => 'Опыт работы в аккредитованной лаборатории'],
            ['title' => 'Главный бухгалтер', 'edu_level' => 'Высшее экономическое', 'requirements' => 'Аттестат, опыт от 5 лет'],
            ['title' => 'Бухгалтер', 'edu_level' => 'Высшее экономическое', 'requirements' => 'Опыт от 2 лет'],
            ['title' => 'Специалист по кадрам', 'edu_level' => 'Высшее', 'requirements' => 'Знание ТК РФ, опыт ведения кадрового учёта'],
            ['title' => 'Системный администратор', 'edu_level' => 'Высшее техническое', 'requirements' => 'Опыт администрирования Windows/Linux'],
        ];

        foreach ($positions as $position) {
            Position::updateOrCreate(['title' => $position['title']], $position);
        }
    }
}
