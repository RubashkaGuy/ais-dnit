<?php

namespace App\Filament\Exports;

use App\Models\Contract;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ContractExporter extends Exporter
{
    protected static ?string $model = Contract::class;

    public function getJobConnection(): ?string
    {
        return 'sync';
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('number')->label('№ договора'),
            ExportColumn::make('date')->label('Дата'),
            ExportColumn::make('client.display_name')->label('Клиент'),
            ExportColumn::make('course.name')->label('Курс'),
            ExportColumn::make('amount')->label('Сумма, ₽'),
            ExportColumn::make('status')->label('Статус'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Экспорт договоров завершён. Успешно: '.number_format($export->successful_rows).'.';

        if (($failed = $export->getFailedRowsCount()) > 0) {
            $body .= ' Не удалось: '.number_format($failed).'.';
        }

        return $body;
    }
}
