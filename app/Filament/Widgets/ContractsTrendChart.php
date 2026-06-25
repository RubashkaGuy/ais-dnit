<?php

namespace App\Filament\Widgets;

use App\Models\Contract;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ContractsTrendChart extends ChartWidget
{
    protected ?string $heading = 'Заключение договоров по месяцам';

    protected ?string $description = 'Количество и сумма договоров за последние 12 месяцев';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $start = Carbon::now()->startOfMonth()->subMonths(11);

        $rows = Contract::query()
            ->selectRaw("DATE_FORMAT(date, '%Y-%m') as month, COUNT(*) as count, COALESCE(SUM(amount), 0) as sum")
            ->where('date', '>=', $start->toDateString())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $labels = [];
        $counts = [];
        $sums = [];

        for ($i = 0; $i < 12; $i++) {
            $month = $start->copy()->addMonths($i);
            $key = $month->format('Y-m');
            $labels[] = $month->translatedFormat('LLL Y');
            $counts[] = (int) ($rows[$key]->count ?? 0);
            $sums[] = (float) ($rows[$key]->sum ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Договоров, шт.',
                    'data' => $counts,
                    'borderColor' => 'rgb(31, 168, 227)',
                    'backgroundColor' => 'rgba(31, 168, 227, 0.15)',
                    'tension' => 0.35,
                    'fill' => true,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Сумма, ₽',
                    'data' => $sums,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.10)',
                    'tension' => 0.35,
                    'borderDash' => [4, 4],
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['position' => 'bottom'],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'position' => 'left',
                    'ticks' => ['precision' => 0],
                    'title' => ['display' => true, 'text' => 'шт.'],
                ],
                'y1' => [
                    'beginAtZero' => true,
                    'position' => 'right',
                    'grid' => ['drawOnChartArea' => false],
                    'title' => ['display' => true, 'text' => '₽'],
                ],
            ],
        ];
    }
}
