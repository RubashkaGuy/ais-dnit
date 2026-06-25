<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Название курса')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                TextInput::make('hours')
                    ->label('Количество часов')
                    ->numeric()
                    ->minValue(1)
                    ->required(),

                TextInput::make('price')
                    ->label('Стоимость, ₽')
                    ->numeric()
                    ->minValue(0)
                    ->step('0.01')
                    ->required()
                    ->prefix('₽'),
            ]);
    }
}
