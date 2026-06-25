<?php

namespace App\Filament\Resources\Activities;

use App\Filament\Resources\Activities\Pages\ListActivities;
use App\Filament\Resources\Activities\Tables\ActivitiesTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $navigationLabel = 'Журнал действий';

    protected static ?string $modelLabel = 'запись журнала';

    protected static ?string $pluralModelLabel = 'Журнал действий';

    protected static string|\UnitEnum|null $navigationGroup = 'Администрирование';

    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return ActivitiesTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return self::isAdmin();
    }

    public static function canView(Model $record): bool
    {
        return self::isAdmin();
    }

    public static function canAccess(): bool
    {
        return self::isAdmin();
    }

    private static function isAdmin(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->isAdmin();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivities::route('/'),
        ];
    }
}
