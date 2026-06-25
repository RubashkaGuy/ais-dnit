<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Concerns\TogglesTableSortDirection;
use App\Filament\Exports\ClientExporter;
use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

class ListClients extends ListRecords
{
    use TogglesTableSortDirection;

    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->label('Экспорт')
                ->exporter(ClientExporter::class),
            CreateAction::make(),
        ];
    }
}
