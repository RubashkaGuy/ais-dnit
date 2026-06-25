<?php

namespace App\Filament\Resources\Activities\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Когда')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('causer.name')
                    ->label('Кто')
                    ->default('—')
                    ->sortable(),

                TextColumn::make('log_name')
                    ->label('Раздел')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'employee' => 'Сотрудник',
                        'client' => 'Клиент',
                        'contract' => 'Договор',
                        'qualification' => 'Квалификация',
                        default => $state ?? '—',
                    })
                    ->color(fn (?string $state) => match ($state) {
                        'employee' => 'primary',
                        'client' => 'info',
                        'contract' => 'success',
                        'qualification' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('event')
                    ->label('Действие')
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'created' => 'Создано',
                        'updated' => 'Изменено',
                        'deleted' => 'Удалено',
                        default => $state ?? '—',
                    })
                    ->badge()
                    ->color(fn (?string $state) => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('subject_id')
                    ->label('Запись #')
                    ->toggleable(),

                TextColumn::make('description')
                    ->label('Описание')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('properties')
                    ->label('Изменения')
                    ->formatStateUsing(function ($state): string {
                        $data = $state instanceof \Illuminate\Support\Collection ? $state->toArray() : (array) $state;
                        $attrs = $data['attributes'] ?? [];
                        $old = $data['old'] ?? [];

                        if (empty($attrs) && empty($old)) {
                            return '—';
                        }

                        $lines = [];
                        foreach ($attrs as $key => $new) {
                            $prev = $old[$key] ?? null;
                            $lines[] = "{$key}: ".(is_scalar($prev) ? $prev : json_encode($prev, JSON_UNESCAPED_UNICODE)).' → '.(is_scalar($new) ? $new : json_encode($new, JSON_UNESCAPED_UNICODE));
                        }

                        return implode("\n", $lines);
                    })
                    ->wrap()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('log_name')
                    ->label('Раздел')
                    ->options([
                        'employee' => 'Сотрудники',
                        'client' => 'Клиенты',
                        'contract' => 'Договоры',
                        'qualification' => 'Квалификации',
                    ]),

                SelectFilter::make('event')
                    ->label('Действие')
                    ->options([
                        'created' => 'Создано',
                        'updated' => 'Изменено',
                        'deleted' => 'Удалено',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->with('causer'));
    }
}
