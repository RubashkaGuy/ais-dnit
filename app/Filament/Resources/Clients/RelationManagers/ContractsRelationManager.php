<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Enums\ContractStatus;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ContractsRelationManager extends RelationManager
{
    protected static string $relationship = 'contracts';

    protected static ?string $title = 'История курсов';

    protected static string|BackedEnum|null $icon = Heroicon::OutlinedAcademicCap;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('number')
            ->columns([
                TextColumn::make('date')
                    ->label('Дата')
                    ->date('d.m.Y')
                    ->sortable(),

                TextColumn::make('course.name')
                    ->label('Курс')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->url(fn ($record) => $record->course
                        ? \App\Filament\Resources\Courses\CourseResource::getUrl('view', ['record' => $record->course])
                        : null),

                TextColumn::make('course.hours')
                    ->label('Часов')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('number')
                    ->label('№ договора')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Сумма')
                    ->money('RUB')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn (ContractStatus $state) => $state->label())
                    ->color(fn (ContractStatus $state) => $state->color()),
            ])
            ->groups([
                Group::make('status')
                    ->label('Статус')
                    ->getTitleFromRecordUsing(fn ($record) => $record->status?->label())
                    ->collapsible(),
                Group::make('date')
                    ->label('Год')
                    ->date('Y')
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->multiple()
                    ->options(collect(ContractStatus::cases())
                        ->mapWithKeys(fn (ContractStatus $s) => [$s->value => $s->label()])
                        ->all()),

                SelectFilter::make('course_id')
                    ->label('Курс')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->relationship('course', 'name'),

                Filter::make('date_range')
                    ->label('Период')
                    ->schema([
                        DatePicker::make('date_from')
                            ->label('С даты')
                            ->native(false)
                            ->displayFormat('d.m.Y'),
                        DatePicker::make('date_to')
                            ->label('По дату')
                            ->native(false)
                            ->displayFormat('d.m.Y'),
                    ])
                    ->columns(2)
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['date_from'] ?? null, fn (Builder $q, $d) => $q->whereDate('date', '>=', $d))
                        ->when($data['date_to'] ?? null, fn (Builder $q, $d) => $q->whereDate('date', '<=', $d)))
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['date_from'] ?? null) {
                            $indicators[] = 'С '.Carbon::parse($data['date_from'])->format('d.m.Y');
                        }
                        if ($data['date_to'] ?? null) {
                            $indicators[] = 'По '.Carbon::parse($data['date_to'])->format('d.m.Y');
                        }

                        return $indicators;
                    }),
            ])
            ->filtersFormColumns(2)
            ->headerActions([])
            ->recordActions([
                Action::make('view_course')
                    ->label('Курс')
                    ->icon(Heroicon::OutlinedBookOpen)
                    ->url(fn ($record) => $record->course
                        ? \App\Filament\Resources\Courses\CourseResource::getUrl('view', ['record' => $record->course])
                        : null),
                Action::make('edit_contract')
                    ->label('Договор')
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->url(fn ($record) => \App\Filament\Resources\Contracts\ContractResource::getUrl('edit', ['record' => $record])),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with('course'))
            ->defaultSort('date', 'desc');
    }
}
