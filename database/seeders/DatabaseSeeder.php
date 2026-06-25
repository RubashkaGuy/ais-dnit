<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            PositionSeeder::class,
            CourseSeeder::class,
            EmployeeSeeder::class,
            QualificationSeeder::class,
            ClientSeeder::class,
            ContractSeeder::class,
        ]);

        User::updateOrCreate(
            ['email' => 'admin@dnit.local'],
            [
                'name' => 'Администратор системы',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@dnit.local'],
            [
                'name' => 'Никифорова О. В.',
                'password' => Hash::make('password'),
                'role' => UserRole::Staff,
            ]
        );
    }
}
