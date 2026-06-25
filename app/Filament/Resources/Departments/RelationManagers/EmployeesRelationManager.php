<?php

namespace App\Filament\Resources\Departments\RelationManagers;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';

    protected static ?string $title = 'Сотрудники подразделения';

    protected static string|BackedEnum|null $icon = Heroicon::OutlinedUsers;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('full_name')
            ->columns([
                TextColumn::make('full_name')
                    ->label('ФИО')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('position.title')
                    ->label('Должность')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('hire_date')
                    ->label('Дата приёма')
                    ->date('d.m.Y')
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Телефон')
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
                Group::make('position.title')
                    ->label('Должность')
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('position_id')
                    ->label('Должность')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->relationship('position', 'title'),
            ])
            ->headerActions([])
            ->recordActions([
                Action::make('open')
                    ->label('Карточка')
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->url(fn ($record) => \App\Filament\Resources\Employees\EmployeeResource::getUrl('edit', ['record' => $record])),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with(['position', 'qualifications']))
            ->defaultSort('full_name');
    }
}
