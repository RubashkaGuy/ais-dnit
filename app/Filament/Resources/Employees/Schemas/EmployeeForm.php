<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основные данные')
                    ->columns(2)
                    ->schema([
                        TextInput::make('full_name')
                            ->label('ФИО')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('position_id')
                            ->label('Должность')
                            ->relationship('position', 'title')
                            ->preload()
                            ->searchable()
                            ->required(),

                        Select::make('department_id')
                            ->label('Подразделение')
                            ->relationship('department', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),

                        DatePicker::make('hire_date')
                            ->label('Дата приёма')
                            ->required()
                            ->native(false)
                            ->displayFormat('d.m.Y'),

                        TextInput::make('phone')
                            ->label('Телефон')
                            ->tel()
                            ->maxLength(50),

                        TextInput::make('education')
                            ->label('Образование')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
