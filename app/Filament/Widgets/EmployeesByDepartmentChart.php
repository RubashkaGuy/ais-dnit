<?php

namespace App\Filament\Widgets;

use App\Models\Department;
use Filament\Widgets\ChartWidget;

class EmployeesByDepartmentChart extends ChartWidget
{
    protected ?string $heading = 'Сотрудники по подразделениям';

    protected ?string $description = 'Распределение штата';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = ['md' => 1, 'xl' => 1];

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $rows = Department::query()
            ->withCount('employees')
            ->orderByDesc('employees_count')
            ->get(['id', 'name']);

        $palette = [
            'rgba(31, 168, 227, 0.85)',
            'rgba(16, 185, 129, 0.85)',
            'rgba(245, 158, 11, 0.85)',
            'rgba(244, 63, 94, 0.85)',
            'rgba(139, 92, 246, 0.85)',
            'rgba(20, 184, 166, 0.85)',
            'rgba(99, 102, 241, 0.85)',
            'rgba(236, 72, 153, 0.85)',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Сотрудников',
                    'data' => $rows->pluck('employees_count')->all(),
                    'backgroundColor' => $rows->keys()
                        ->map(fn ($i) => $palette[$i % count($palette)])
                        ->all(),
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $rows->pluck('name')->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['position' => 'right'],
            ],
            'cutout' => '60%',
        ];
    }
}
