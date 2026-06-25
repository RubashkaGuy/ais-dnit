<?php

namespace App\Filament\Resources\Contracts\Schemas;

use App\Enums\ContractStatus;
use App\Models\Course;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ContractForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Стороны и предмет договора')
                    ->columns(2)
                    ->schema([
                        Select::make('client_id')
                            ->label('Клиент')
                            ->relationship('client', 'full_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->display_name ?: ('Клиент №'.$record->id))
                            ->searchable(['full_name', 'org_name', 'inn'])
                            ->preload()
                            ->required(),

                        Select::make('course_id')
                            ->label('Курс обучения')
                            ->relationship('course', 'name')
                            ->preload()
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state && $course = Course::find($state)) {
                                    $set('amount', (string) $course->price);
                                }
                            }),
                    ]),

                Section::make('Реквизиты договора')
                    ->columns(2)
                    ->schema([
                        TextInput::make('number')
                            ->label('Номер договора')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),

                        DatePicker::make('date')
                            ->label('Дата заключения')
                            ->required()
                            ->native(false)
                            ->displayFormat('d.m.Y'),

                        TextInput::make('amount')
                            ->label('Сумма, ₽')
                            ->numeric()
                            ->minValue(0)
                            ->step('0.01')
                            ->required()
                            ->prefix('₽'),

                        Select::make('status')
                            ->label('Статус')
                            ->options(collect(ContractStatus::cases())
                                ->mapWithKeys(fn (ContractStatus $s) => [$s->value => $s->label()])
                                ->all())
                            ->default(ContractStatus::Pending->value)
                            ->required(),
                    ]),

                Section::make('Документы')
                    ->schema([
                        FileUpload::make('scan_path')
                            ->label('Скан подписанного договора')
                            ->directory('contracts')
                            ->disk('public')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->maxSize(10240)
                            ->openable()
                            ->downloadable(),
                    ])
                    ->collapsible(),
            ]);
    }
}
