<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Enums\ClientType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Тип клиента')
                    ->schema([
                        Select::make('type')
                            ->label('Тип клиента')
                            ->options(collect(ClientType::cases())
                                ->mapWithKeys(fn (ClientType $t) => [$t->value => $t->label()])
                                ->all())
                            ->default(ClientType::Individual->value)
                            ->required()
                            ->live(),
                    ]),

                Section::make('Реквизиты')
                    ->columns(2)
                    ->schema([
                        TextInput::make('full_name')
                            ->label('ФИО')
                            ->maxLength(255)
                            ->required(fn (Get $get) => $get('type') === ClientType::Individual->value)
                            ->visible(fn (Get $get) => $get('type') === ClientType::Individual->value),

                        TextInput::make('org_name')
                            ->label('Наименование организации')
                            ->maxLength(255)
                            ->required(fn (Get $get) => $get('type') === ClientType::Company->value)
                            ->visible(fn (Get $get) => $get('type') === ClientType::Company->value),

                        TextInput::make('inn')
                            ->label('ИНН')
                            ->maxLength(12),

                        TextInput::make('phone')
                            ->label('Телефон')
                            ->tel()
                            ->maxLength(50),

                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->maxLength(255),
                    ]),
            ]);
    }
}
