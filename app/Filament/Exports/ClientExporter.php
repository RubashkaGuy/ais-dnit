<?php

namespace App\Filament\Exports;

use App\Models\Client;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ClientExporter extends Exporter
{
    protected static ?string $model = Client::class;

    public function getJobConnection(): ?string
    {
        return 'sync';
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('type')->label('Тип'),
            ExportColumn::make('full_name')->label('ФИО'),
            ExportColumn::make('org_name')->label('Организация'),
            ExportColumn::make('inn')->label('ИНН'),
            ExportColumn::make('phone')->label('Телефон'),
            ExportColumn::make('email')->label('E-mail'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Экспорт клиентов завершён. Успешно: '.number_format($export->successful_rows).'.';

        if (($failed = $export->getFailedRowsCount()) > 0) {
            $body .= ' Не удалось: '.number_format($failed).'.';
        }

        return $body;
    }
}
