<?php

namespace App\Filament\Resources\Clients\Tables;

use App\Enums\ClientType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Тип')
                    ->badge()
                    ->formatStateUsing(fn (ClientType $state) => $state->label())
                    ->color(fn (ClientType $state) => $state === ClientType::Company ? 'info' : 'success'),

                TextColumn::make('display_name')
                    ->label('ФИО / Организация')
                    ->searchable(['full_name', 'org_name'])
                    ->sortable(['full_name']),

                TextColumn::make('inn')
                    ->label('ИНН')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->toggleable()
                    ->copyable(),

                TextColumn::make('contracts_count')
                    ->label('Договоров')
                    ->counts('contracts')
                    ->sortable(),

                TextColumn::make('contracts_sum_amount')
                    ->label('Сумма договоров')
                    ->sum('contracts', 'amount')
                    ->money('RUB')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Добавлен')
                    ->date('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Тип клиента')
                    ->options(collect(ClientType::cases())
                        ->mapWithKeys(fn (ClientType $t) => [$t->value => $t->label()])
                        ->all()),

                SelectFilter::make('course_id')
                    ->label('Прошёл курс')
                    ->preload()
                    ->searchable()
                    ->options(fn () => \App\Models\Course::query()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['value'] ?? null, fn (Builder $q, $courseId) => $q
                            ->whereHas('contracts', fn (Builder $cq) => $cq->where('course_id', $courseId)))),

                TernaryFilter::make('has_contracts')
                    ->label('Наличие договоров')
                    ->placeholder('Все')
                    ->trueLabel('С договорами')
                    ->falseLabel('Без договоров')
                    ->queries(
                        true: fn (Builder $q) => $q->has('contracts'),
                        false: fn (Builder $q) => $q->doesntHave('contracts'),
                    ),

                TernaryFilter::make('has_email')
                    ->label('E-mail')
                    ->placeholder('Все')
                    ->trueLabel('Указан')
                    ->falseLabel('Не указан')
                    ->queries(
                        true: fn (Builder $q) => $q->whereNotNull('email')->where('email', '!=', ''),
                        false: fn (Builder $q) => $q->where(fn (Builder $q2) => $q2->whereNull('email')->orWhere('email', '')),
                    ),

                TernaryFilter::make('has_phone')
                    ->label('Телефон')
                    ->placeholder('Все')
                    ->trueLabel('Указан')
                    ->falseLabel('Не указан')
                    ->queries(
                        true: fn (Builder $q) => $q->whereNotNull('phone')->where('phone', '!=', ''),
                        false: fn (Builder $q) => $q->where(fn (Builder $q2) => $q2->whereNull('phone')->orWhere('phone', '')),
                    ),
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
            ->defaultSort('created_at', 'desc');
    }
}
