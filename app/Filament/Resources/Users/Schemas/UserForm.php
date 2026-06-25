<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Имя пользователя')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                TextInput::make('password')
                    ->label('Пароль')
                    ->password()
                    ->revealable()
                    ->minLength(8)
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn (?string $state): bool => filled($state)),

                Select::make('role')
                    ->label('Роль')
                    ->options(collect(UserRole::cases())
                        ->mapWithKeys(fn (UserRole $r) => [$r->value => $r->label()])
                        ->all())
                    ->default(UserRole::Staff->value)
                    ->required(),

                Select::make('employee_id')
                    ->label('Связанный сотрудник')
                    ->relationship('employee', 'full_name')
                    ->searchable()
                    ->preload(),
            ]);
    }
}
