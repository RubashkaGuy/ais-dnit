<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Enums\ClientType;
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

    protected static ?string $title = 'Клиенты на курсе';

    protected static string|BackedEnum|null $icon = Heroicon::OutlinedUsers;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('number')
            ->columns([
                TextColumn::make('client.display_name')
                    ->label('Клиент')
                    ->searchable(['clients.full_name', 'clients.org_name'])
                    ->sortable()
                    ->wrap()
                    ->url(fn ($record) => $record->client
                        ? \App\Filament\Resources\Clients\ClientResource::getUrl('view', ['record' => $record->client])
                        : null),

                TextColumn::make('client.type')
                    ->label('Тип')
                    ->badge()
                    ->formatStateUsing(fn (?ClientType $state) => $state?->label())
                    ->color(fn (?ClientType $state) => $state === ClientType::Company ? 'info' : 'success'),

                TextColumn::make('client.phone')
                    ->label('Телефон')
                    ->toggleable(),

                TextColumn::make('client.email')
                    ->label('E-mail')
                    ->toggleable()
                    ->copyable(),

                TextColumn::make('number')
                    ->label('№ договора')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Дата')
                    ->date('d.m.Y')
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
                Group::make('client.type')
                    ->label('Тип клиента')
                    ->getTitleFromRecordUsing(fn ($record) => $record->client?->type?->label())
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->multiple()
                    ->options(collect(ContractStatus::cases())
                        ->mapWithKeys(fn (ContractStatus $s) => [$s->value => $s->label()])
                        ->all()),

                SelectFilter::make('client_type')
                    ->label('Тип клиента')
                    ->options(collect(ClientType::cases())
                        ->mapWithKeys(fn (ClientType $t) => [$t->value => $t->label()])
                        ->all())
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['value'] ?? null, fn (Builder $q, $type) => $q
                            ->whereHas('client', fn (Builder $cq) => $cq->where('type', $type)))),

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
                Action::make('view_client')
                    ->label('Клиент')
                    ->icon(Heroicon::OutlinedUser)
                    ->url(fn ($record) => $record->client
                        ? \App\Filament\Resources\Clients\ClientResource::getUrl('view', ['record' => $record->client])
                        : null)
                    ->openUrlInNewTab(false),
                Action::make('edit_contract')
                    ->label('Договор')
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->url(fn ($record) => \App\Filament\Resources\Contracts\ContractResource::getUrl('edit', ['record' => $record])),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with('client'))
            ->defaultSort('date', 'desc');
    }
}
