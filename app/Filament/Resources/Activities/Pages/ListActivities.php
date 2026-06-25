<?php

namespace App\Filament\Resources\Activities\Pages;

use App\Filament\Concerns\TogglesTableSortDirection;
use App\Filament\Resources\Activities\ActivityResource;
use Filament\Resources\Pages\ListRecords;

class ListActivities extends ListRecords
{
    use TogglesTableSortDirection;

    protected static string $resource = ActivityResource::class;
}
