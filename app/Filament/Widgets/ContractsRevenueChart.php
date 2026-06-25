<?php

namespace App\Filament\Widgets;

use App\Enums\ContractStatus;
use App\Models\Contract;
use Filament\Widgets\ChartWidget;

class ContractsRevenueChart extends ChartWidget
{
    protected ?string $heading = 'Финансовый срез договоров';

    protected ?string $description = 'Суммы договоров в разрезе статуса';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = ['md' => 1, 'xl' => 1];

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $sums = Contract::query()
            ->selectRaw('status, COALESCE(SUM(amount), 0) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        $labels = [];
        $data = [];
        $colors = [];

        foreach (ContractStatus::cases() as $status) {
            $labels[] = $status->label();
            $data[] = (float) ($sums[$status->value] ?? 0);
            $colors[] = match ($status) {
                ContractStatus::Pending => 'rgba(245, 158, 11, 0.7)',
                ContractStatus::Paid => 'rgba(14, 165, 233, 0.7)',
                ContractStatus::Studying => 'rgba(31, 168, 227, 0.7)',
                ContractStatus::Completed => 'rgba(16, 185, 129, 0.7)',
            };
        }

        return [
            'datasets' => [
                [
                    'label' => 'Сумма, ₽',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => ['beginAtZero' => true],
            ],
        ];
    }
}
