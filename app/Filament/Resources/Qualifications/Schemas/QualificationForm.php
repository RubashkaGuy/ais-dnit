<?php

namespace App\Filament\Resources\Qualifications\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class QualificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('employee_id')
                    ->label('Сотрудник')
                    ->relationship('employee', 'full_name')
                    ->preload()
                    ->searchable()
                    ->required(),

                TextInput::make('course_name')
                    ->label('Программа обучения')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                DatePicker::make('date')
                    ->label('Дата прохождения')
                    ->required()
                    ->native(false)
                    ->displayFormat('d.m.Y')
                    ->maxDate(Carbon::now())
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set('next_date', Carbon::parse($state)->addYears(3)->toDateString());
                        }
                    }),

                DatePicker::make('next_date')
                    ->label('Дата следующего повышения')
                    ->required()
                    ->native(false)
                    ->displayFormat('d.m.Y')
                    ->helperText('Автоматически: дата + 3 года (ст. 47 ФЗ № 273-ФЗ)'),

                FileUpload::make('scan_path')
                    ->label('Скан диплома / удостоверения')
                    ->directory('qualifications')
                    ->disk('public')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                    ->maxSize(10240)
                    ->openable()
                    ->downloadable()
                    ->columnSpanFull(),
            ]);
    }
}
