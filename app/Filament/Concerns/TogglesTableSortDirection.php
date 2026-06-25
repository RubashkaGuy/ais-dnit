<?php

namespace App\Filament\Concerns;

trait TogglesTableSortDirection
{
    public function sortTable(?string $column = null, ?string $direction = null): void
    {
        if ($column === $this->getTableSortColumn()) {
            $direction ??= $this->getTableSortDirection() === 'asc' ? 'desc' : 'asc';
        } else {
            $direction ??= 'asc';
        }

        $this->tableSort = "{$column}:{$direction}";

        $this->updatedTableSort();
    }
}
