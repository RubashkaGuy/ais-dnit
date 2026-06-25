<?php

namespace App\Filament\Resources\Contracts\Pages;

use App\Filament\Concerns\TogglesTableSortDirection;
use App\Filament\Exports\ContractExporter;
use App\Filament\Resources\Contracts\ContractResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

class ListContracts extends ListRecords
{
    use TogglesTableSortDirection;

    protected static string $resource = ContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->label('Экспорт')
                ->exporter(ContractExporter::class),
            CreateAction::make(),
        ];
    }
}
