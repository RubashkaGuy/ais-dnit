<?php

namespace App\Filament\Resources\Courses\Tables;

use App\Enums\ContractStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Название курса')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('hours')
                    ->label('Часов')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Стоимость')
                    ->money('RUB')
                    ->sortable(),

                TextColumn::make('contracts_count')
                    ->label('Договоров')
                    ->counts('contracts')
                    ->sortable(),

                TextColumn::make('paid_contracts_count')
                    ->label('Оплачено')
                    ->counts([
                        'contracts as paid_contracts_count' => fn (Builder $q) => $q
                            ->whereIn('status', [
                                ContractStatus::Paid->value,
                                ContractStatus::Studying->value,
                                ContractStatus::Completed->value,
                            ]),
                    ])
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('total_revenue')
                    ->label('Выручка')
                    ->sum([
                        'contracts as total_revenue' => fn (Builder $q) => $q
                            ->whereIn('status', [
                                ContractStatus::Paid->value,
                                ContractStatus::Studying->value,
                                ContractStatus::Completed->value,
                            ]),
                    ], 'amount')
                    ->money('RUB')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('price_range')
                    ->label('Диапазон цены')
                    ->schema([
                        TextInput::make('price_min')
                            ->label('Цена от, ₽')
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('price_max')
                            ->label('Цена до, ₽')
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->columns(2)
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['price_min'] ?? null, fn (Builder $q, $v) => $q->where('price', '>=', $v))
                        ->when($data['price_max'] ?? null, fn (Builder $q, $v) => $q->where('price', '<=', $v)))
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['price_min'] ?? null) {
                            $indicators[] = 'Цена от '.number_format((float) $data['price_min'], 0, ',', ' ').' ₽';
                        }
                        if ($data['price_max'] ?? null) {
                            $indicators[] = 'Цена до '.number_format((float) $data['price_max'], 0, ',', ' ').' ₽';
                        }

                        return $indicators;
                    }),

                Filter::make('hours_range')
                    ->label('Количество часов')
                    ->schema([
                        TextInput::make('hours_min')
                            ->label('Часов от')
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('hours_max')
                            ->label('Часов до')
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->columns(2)
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['hours_min'] ?? null, fn (Builder $q, $v) => $q->where('hours', '>=', $v))
                        ->when($data['hours_max'] ?? null, fn (Builder $q, $v) => $q->where('hours', '<=', $v))),

                Filter::make('has_contracts')
                    ->label('Есть договоры')
                    ->query(fn (Builder $query) => $query->has('contracts')),
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
            ->defaultSort('name');
    }
}
