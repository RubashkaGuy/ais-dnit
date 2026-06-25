<?php

namespace App\Filament\Resources\Contracts\Pages;

use App\Filament\Resources\Contracts\ContractResource;
use App\Models\Contract;
use App\Services\ContractDocumentGenerator;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Throwable;

class EditContract extends EditRecord
{
    protected static string $resource = ContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadDocx')
                ->label('Скачать договор (DOCX)')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('primary')
                ->action(function () {
                    /** @var Contract $contract */
                    $contract = $this->getRecord();

                    try {
                        return app(ContractDocumentGenerator::class)->download($contract);
                    } catch (Throwable $e) {
                        Notification::make()
                            ->title('Не удалось сгенерировать договор')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }

                    return null;
                }),

            DeleteAction::make(),
        ];
    }
}
