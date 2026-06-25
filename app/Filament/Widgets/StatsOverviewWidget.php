<?php

namespace App\Filament\Widgets;

use App\Enums\ContractStatus;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Course;
use App\Models\Employee;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    protected ?string $heading = 'Ключевые показатели';

    protected function getStats(): array
    {
        $expiringSoon = Employee::query()
            ->whereHas('qualifications', fn ($q) => $q->whereDate('next_date', '<=', Carbon::now()->addDays(90)))
            ->orWhereDoesntHave('qualifications')
            ->count();

        $unpaid = Contract::query()
            ->where('status', ContractStatus::Pending)
            ->sum('amount');

        $paidStatuses = [
            ContractStatus::Paid->value,
            ContractStatus::Studying->value,
            ContractStatus::Completed->value,
        ];

        $topCourse = Course::query()
            ->withSum(
                ['contracts as revenue' => fn ($q) => $q->whereIn('status', $paidStatuses)],
                'amount',
            )
            ->orderByDesc('revenue')
            ->first();

        $topCourseRevenue = (float) ($topCourse?->revenue ?? 0);

        return [
            Stat::make('Сотрудников', Employee::count())
                ->description('Всего в штате')
                ->icon(Heroicon::OutlinedUsers)
                ->color('primary'),

            Stat::make('Клиентов', Client::count())
                ->description('Физические и юридические лица')
                ->icon(Heroicon::OutlinedIdentification)
                ->color('info'),

            Stat::make('Договоров', Contract::count())
                ->description('Всего заключено')
                ->icon(Heroicon::OutlinedDocumentText)
                ->color('success'),

            Stat::make('Требуют повышения квалификации', $expiringSoon)
                ->description('В ближайшие 90 дней или просрочено')
                ->icon(Heroicon::OutlinedExclamationTriangle)
                ->color($expiringSoon > 0 ? 'danger' : 'success'),

            Stat::make('Ожидают оплаты', number_format((float) $unpaid, 0, ',', ' ').' ₽')
                ->description('Сумма по неоплаченным договорам')
                ->icon(Heroicon::OutlinedBanknotes)
                ->color('warning'),

            Stat::make(
                'Самый прибыльный курс',
                $topCourse && $topCourseRevenue > 0
                    ? number_format($topCourseRevenue, 0, ',', ' ').' ₽'
                    : '—',
            )
                ->description($topCourse?->name ?? 'Нет данных')
                ->icon(Heroicon::OutlinedTrophy)
                ->color('success'),
        ];
    }
}
