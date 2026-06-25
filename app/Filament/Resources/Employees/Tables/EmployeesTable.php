<?php

namespace App\Filament\Resources\Employees\Tables;

use App\Models\Employee;
use Carbon\CarbonInterface;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class EmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('ФИО')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('position.title')
                    ->label('Должность')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('department.name')
                    ->label('Подразделение')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('hire_date')
                    ->label('Дата приёма')
                    ->date('d.m.Y')
                    ->sortable(),

                TextColumn::make('education')
                    ->label('Образование')
                    ->toggleable()
                    ->limit(40),

                TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('next_qualification_date')
                    ->label('След. повышение квалификации')
                    ->date('d.m.Y')
                    ->badge()
                    ->color(fn (?CarbonInterface $state) => match (true) {
                        $state === null => 'gray',
                        $state->isPast() => 'danger',
                        $state->lte(Carbon::now()->addDays(90)) => 'warning',
                        default => 'success',
                    })
                    ->placeholder('—'),
            ])
            ->groups([
                Group::make('department.name')
                    ->label('Подразделение')
                    ->collapsible(),
                Group::make('position.title')
                    ->label('Должность')
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('department_id')
                    ->label('Подразделение')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->relationship('department', 'name'),

                SelectFilter::make('position_id')
                    ->label('Должность')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->relationship('position', 'title'),

                Filter::make('hire_date_range')
                    ->label('Период приёма')
                    ->schema([
                        DatePicker::make('hire_from')
                            ->label('С даты')
                            ->native(false)
                            ->displayFormat('d.m.Y'),
                        DatePicker::make('hire_to')
                            ->label('По дату')
                            ->native(false)
                            ->displayFormat('d.m.Y'),
                    ])
                    ->columns(2)
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['hire_from'] ?? null, fn (Builder $q, $d) => $q->whereDate('hire_date', '>=', $d))
                        ->when($data['hire_to'] ?? null, fn (Builder $q, $d) => $q->whereDate('hire_date', '<=', $d)))
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['hire_from'] ?? null) {
                            $indicators[] = 'Приём с '.Carbon::parse($data['hire_from'])->format('d.m.Y');
                        }
                        if ($data['hire_to'] ?? null) {
                            $indicators[] = 'Приём по '.Carbon::parse($data['hire_to'])->format('d.m.Y');
                        }

                        return $indicators;
                    }),

                SelectFilter::make('qualification_state')
                    ->label('Статус квалификации')
                    ->options([
                        'overdue' => 'Просрочена',
                        'soon' => 'В ближайшие 90 дней',
                        'ok' => 'В норме',
                        'never' => 'Нет данных',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;
                        if (! $value) {
                            return $query;
                        }

                        $today = Carbon::now()->toDateString();
                        $in90 = Carbon::now()->addDays(90)->toDateString();

                        return match ($value) {
                            'overdue' => $query->whereHas('qualifications', fn (Builder $q) => $q
                                ->whereDate('next_date', '<', $today)),
                            'soon' => $query->whereHas('qualifications', fn (Builder $q) => $q
                                ->whereDate('next_date', '>=', $today)
                                ->whereDate('next_date', '<=', $in90)),
                            'ok' => $query->whereHas('qualifications', fn (Builder $q) => $q
                                ->whereDate('next_date', '>', $in90)),
                            'never' => $query->whereDoesntHave('qualifications'),
                            default => $query,
                        };
                    }),
            ])
            ->filtersFormColumns(2)
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with(['position', 'department', 'qualifications']))
            ->defaultSort('full_name');
    }
}
