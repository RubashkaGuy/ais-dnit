<?php

namespace App\Filament\Resources\Qualifications\Tables;

use Carbon\CarbonInterface;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class QualificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Сотрудник')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('course_name')
                    ->label('Программа обучения')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('date')
                    ->label('Дата прохождения')
                    ->date('d.m.Y')
                    ->sortable(),

                TextColumn::make('next_date')
                    ->label('Следующее повышение')
                    ->date('d.m.Y')
                    ->badge()
                    ->color(fn (?CarbonInterface $state) => match (true) {
                        $state === null => 'gray',
                        $state->isPast() => 'danger',
                        $state->lte(Carbon::now()->addDays(90)) => 'warning',
                        default => 'success',
                    })
                    ->sortable(),

                IconColumn::make('scan_path')
                    ->label('Скан')
                    ->icon(fn (?string $state): string => $state
                        ? Heroicon::OutlinedDocumentArrowDown->value
                        : Heroicon::OutlinedMinus->value)
                    ->color(fn (?string $state) => $state ? 'primary' : 'gray')
                    ->url(fn ($record) => $record->scan_path
                        ? \Illuminate\Support\Facades\Storage::disk('public')->url($record->scan_path)
                        : null, shouldOpenInNewTab: true)
                    ->toggleable(),
            ])
            ->filters([
                Filter::make('expiring_soon')
                    ->label('Срок истекает в 90 дней')
                    ->query(fn ($query) => $query->whereDate('next_date', '<=', Carbon::now()->addDays(90))),
                Filter::make('expired')
                    ->label('Срок истёк')
                    ->query(fn ($query) => $query->whereDate('next_date', '<', Carbon::now())),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('next_date');
    }
}
