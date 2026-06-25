<?php

namespace App\Filament\Resources\Positions\Pages;

use App\Filament\Concerns\TogglesTableSortDirection;
use App\Filament\Resources\Positions\PositionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPositions extends ListRecords
{
    use TogglesTableSortDirection;

    protected static string $resource = PositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
