<?php

namespace App\Filament\Resources\Positions\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PositionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Название должности')
                    ->required()
                    ->maxLength(255),

                TextInput::make('edu_level')
                    ->label('Требуемый уровень образования')
                    ->maxLength(255),

                Textarea::make('requirements')
                    ->label('Требования к должности')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }
}
