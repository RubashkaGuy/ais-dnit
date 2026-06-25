<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\TogglesTableSortDirection;
use App\Models\Employee;
use Carbon\CarbonInterface;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class QualificationControlWidget extends TableWidget
{
    use TogglesTableSortDirection;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function getTableHeading(): string
    {
        return 'Контроль повышения квалификации (ближайшие 90 дней)';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Employee::query()
                ->with(['position', 'department', 'qualifications'])
                ->where(function (Builder $query): void {
                    $query
                        ->whereHas('qualifications', fn ($q) => $q->whereDate('next_date', '<=', Carbon::now()->addDays(90)))
                        ->orWhereDoesntHave('qualifications');
                })
            )
            ->searchPlaceholder('Поиск по ФИО, должности, подразделению')
            ->searchDebounce('300ms')
            ->columns([
                TextColumn::make('full_name')
                    ->label('ФИО')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('position.title')
                    ->label('Должность')
                    ->searchable(),

                TextColumn::make('department.name')
                    ->label('Подразделение')
                    ->searchable(),

                TextColumn::make('next_qualification_date')
                    ->label('Дата следующего повышения')
                    ->date('d.m.Y')
                    ->badge()
                    ->color(fn (?CarbonInterface $state) => match (true) {
                        $state === null => 'danger',
                        $state->isPast() => 'danger',
                        $state->lte(Carbon::now()->addDays(30)) => 'warning',
                        default => 'info',
                    })
                    ->placeholder('Нет данных')
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query
                        ->leftJoin('qualifications', 'qualifications.employee_id', '=', 'employees.id')
                        ->select('employees.*')
                        ->selectRaw('MAX(qualifications.next_date) as latest_next_date')
                        ->groupBy('employees.id')
                        ->orderBy('latest_next_date', $direction)),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('Все сотрудники прошли повышение квалификации')
            ->emptyStateDescription('У всех сотрудников срок следующего повышения квалификации больше 90 дней.');
    }
}
