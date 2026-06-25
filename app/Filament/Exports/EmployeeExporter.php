<?php

namespace App\Filament\Exports;

use App\Models\Employee;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class EmployeeExporter extends Exporter
{
    protected static ?string $model = Employee::class;

    public function getJobConnection(): ?string
    {
        return 'sync';
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('full_name')->label('ФИО'),
            ExportColumn::make('position.title')->label('Должность'),
            ExportColumn::make('department.name')->label('Подразделение'),
            ExportColumn::make('hire_date')->label('Дата приёма'),
            ExportColumn::make('education')->label('Образование'),
            ExportColumn::make('phone')->label('Телефон'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Экспорт сотрудников завершён. Успешно: '.number_format($export->successful_rows).'.';

        if (($failed = $export->getFailedRowsCount()) > 0) {
            $body .= ' Не удалось: '.number_format($failed).'.';
        }

        return $body;
    }
}
