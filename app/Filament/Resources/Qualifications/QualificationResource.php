<?php

namespace App\Filament\Resources\Qualifications;

use App\Filament\Resources\Qualifications\Pages\CreateQualification;
use App\Filament\Resources\Qualifications\Pages\EditQualification;
use App\Filament\Resources\Qualifications\Pages\ListQualifications;
use App\Filament\Resources\Qualifications\Schemas\QualificationForm;
use App\Filament\Resources\Qualifications\Tables\QualificationsTable;
use App\Models\Qualification;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QualificationResource extends Resource
{
    protected static ?string $model = Qualification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Повышение квалификации';

    protected static ?string $modelLabel = 'запись о повышении квалификации';

    protected static ?string $pluralModelLabel = 'Повышение квалификации';

    protected static string|\UnitEnum|null $navigationGroup = 'Учёт кадров';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'course_name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['course_name', 'employee.full_name'];
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->course_name.' — '.($record->employee?->full_name ?? 'Сотрудник не указан');
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return array_filter([
            'Дата' => $record->date?->format('d.m.Y'),
            'Следующая' => $record->next_date?->format('d.m.Y'),
        ]);
    }

    public static function getGlobalSearchEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with('employee');
    }

    public static function form(Schema $schema): Schema
    {
        return QualificationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QualificationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQualifications::route('/'),
            'create' => CreateQualification::route('/create'),
            'edit' => EditQualification::route('/{record}/edit'),
        ];
    }
}
