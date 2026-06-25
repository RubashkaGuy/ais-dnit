<?php

namespace App\Filament\Resources\Contracts\Tables;

use App\Enums\ClientType;
use App\Enums\ContractStatus;
use App\Services\ContractDocumentGenerator;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

class ContractsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('№ договора')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Дата')
                    ->date('d.m.Y')
                    ->sortable(),

                TextColumn::make('client.display_name')
                    ->label('Клиент')
                    ->searchable(['clients.full_name', 'clients.org_name'])
                    ->wrap(),

                TextColumn::make('course.name')
                    ->label('Курс')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('amount')
                    ->label('Сумма')
                    ->money('RUB')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn (ContractStatus $state) => $state->label())
                    ->color(fn (ContractStatus $state) => $state->color()),

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

                SelectFilter::make('client_id')
                    ->label('Клиент')
                    ->preload()
                    ->searchable()
                    ->relationship('client', 'full_name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->display_name ?: ('Клиент №'.$record->id)),

                SelectFilter::make('client_type')
                    ->label('Тип клиента')
                    ->options(collect(ClientType::cases())
                        ->mapWithKeys(fn (ClientType $t) => [$t->value => $t->label()])
                        ->all())
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['value'] ?? null, fn (Builder $q, string $type) => $q
                            ->whereHas('client', fn (Builder $cq) => $cq->where('type', $type)))),

                Filter::make('date_range')
                    ->label('Период заключения')
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
                        ->when($data['date_from'] ?? null, fn (Builder $q, $date) => $q->whereDate('date', '>=', $date))
                        ->when($data['date_to'] ?? null, fn (Builder $q, $date) => $q->whereDate('date', '<=', $date)))
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['date_from'] ?? null) {
                            $indicators[] = 'С '.\Illuminate\Support\Carbon::parse($data['date_from'])->format('d.m.Y');
                        }
                        if ($data['date_to'] ?? null) {
                            $indicators[] = 'По '.\Illuminate\Support\Carbon::parse($data['date_to'])->format('d.m.Y');
                        }

                        return $indicators;
                    }),

                Filter::make('amount_range')
                    ->label('Сумма договора')
                    ->schema([
                        TextInput::make('amount_min')
                            ->label('От, ₽')
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('amount_max')
                            ->label('До, ₽')
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->columns(2)
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['amount_min'] ?? null, fn (Builder $q, $v) => $q->where('amount', '>=', $v))
                        ->when($data['amount_max'] ?? null, fn (Builder $q, $v) => $q->where('amount', '<=', $v)))
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['amount_min'] ?? null) {
                            $indicators[] = 'От '.number_format((float) $data['amount_min'], 0, ',', ' ').' ₽';
                        }
                        if ($data['amount_max'] ?? null) {
                            $indicators[] = 'До '.number_format((float) $data['amount_max'], 0, ',', ' ').' ₽';
                        }

                        return $indicators;
                    }),

                Filter::make('has_scan')
                    ->label('Со сканом')
                    ->query(fn (Builder $query) => $query->whereNotNull('scan_path')),
            ])
            ->filtersFormColumns(2)
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->recordActions([
                Action::make('downloadDocx')
                    ->label('DOCX')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->color('primary')
                    ->action(function ($record) {
                        try {
                            return app(ContractDocumentGenerator::class)->download($record);
                        } catch (Throwable $e) {
                            Notification::make()
                                ->title('Не удалось сгенерировать договор')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }

                        return null;
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with(['client', 'course']))
            ->defaultSort('date', 'desc');
    }
}
