<?php

namespace App\Filament\Resources\Departments\Pages;

use App\Filament\Concerns\TogglesTableSortDirection;
use App\Filament\Resources\Departments\DepartmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDepartments extends ListRecords
{
    use TogglesTableSortDirection;

    protected static string $resource = DepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
