<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Concerns\TogglesTableSortDirection;
use App\Filament\Exports\EmployeeExporter;
use App\Filament\Resources\Employees\EmployeeResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

class ListEmployees extends ListRecords
{
    use TogglesTableSortDirection;

    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->label('Экспорт')
                ->exporter(EmployeeExporter::class),
            CreateAction::make(),
        ];
    }
}
