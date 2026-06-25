<?php

namespace App\Filament\Widgets;

use App\Enums\ContractStatus;
use App\Models\Course;
use Filament\Widgets\ChartWidget;

class TopCoursesRevenueChart extends ChartWidget
{
    protected ?string $heading = 'Самые прибыльные курсы';

    protected ?string $description = 'Топ-10 по выручке (оплаченные, обучается, завершён)';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = ['md' => 2, 'xl' => 2];

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $paidStatuses = [
            ContractStatus::Paid->value,
            ContractStatus::Studying->value,
            ContractStatus::Completed->value,
        ];

        $rows = Course::query()
            ->withSum(
                ['contracts as revenue' => fn ($q) => $q->whereIn('status', $paidStatuses)],
                'amount',
            )
            ->orderByDesc('revenue')
            ->limit(10)
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
            'rgba(34, 197, 94, 0.85)',
            'rgba(168, 85, 247, 0.85)',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Выручка, ₽',
                    'data' => $rows->pluck('revenue')->map(fn ($v) => (float) ($v ?? 0))->all(),
                    'backgroundColor' => $rows->keys()
                        ->map(fn ($i) => $palette[$i % count($palette)])
                        ->all(),
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $rows->pluck('name')->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'x' => ['beginAtZero' => true],
            ],
        ];
    }
}
