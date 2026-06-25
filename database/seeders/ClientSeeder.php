<?php

namespace Database\Seeders;

use App\Enums\ClientType;
use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            ['ООО «Волгоградтрансгаз»', '3445000125', '+7 (8442) 32-11-22', 'office@vtg-vlg.ru'],
            ['АО «Каустик»', '3447001234', '+7 (8442) 40-50-60', 'info@kaustik.ru'],
            ['ОАО «Волгограднефтемаш»', '3444005678', '+7 (8442) 22-33-44', 'sekretar@vnm-vlg.ru'],
            ['ООО «СтройМонтаж 34»', '3446009876', '+7 (8442) 55-66-77', 'sm34@yandex.ru'],
            ['МУП «Волгоградское коммунальное хозяйство»', '3445112233', '+7 (8442) 11-22-33', 'mup@vkh.ru'],
            ['ООО «Южная энергетическая компания»', '3444223344', '+7 (8442) 77-88-99', 'sek@uek34.ru'],
            ['ИП Петров А. С.', '344500334455', '+7 (937) 111-22-33', 'petrov@mail.ru'],
            ['ООО «Волгоградский завод тракторных деталей»', '3447334455', '+7 (8442) 60-70-80', 'info@vztd.ru'],
        ];

        foreach ($companies as [$name, $inn, $phone, $email]) {
            Client::updateOrCreate(
                ['inn' => $inn],
                [
                    'type' => ClientType::Company,
                    'org_name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                ]
            );
        }

        $individuals = [
            ['Кравцов Сергей Юрьевич', '344512345601', '+7 (937) 200-30-01', 'kravtsov@mail.ru'],
            ['Захарова Елена Михайловна', '344512345602', '+7 (937) 200-30-02', 'zaharova.em@gmail.com'],
            ['Степанов Алексей Иванович', '344512345603', '+7 (937) 200-30-03', 'stepanov@yandex.ru'],
            ['Антонова Марина Петровна', '344512345604', '+7 (937) 200-30-04', 'antonova.mp@mail.ru'],
            ['Бочкарёв Денис Васильевич', '344512345605', '+7 (937) 200-30-05', 'bochkarev@yandex.ru'],
        ];

        foreach ($individuals as [$name, $inn, $phone, $email]) {
            Client::updateOrCreate(
                ['inn' => $inn],
                [
                    'type' => ClientType::Individual,
                    'full_name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                ]
            );
        }
    }
}
