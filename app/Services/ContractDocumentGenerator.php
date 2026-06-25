<?php

namespace App\Services;

use App\Models\Contract;
use PhpOffice\PhpWord\TemplateProcessor;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ContractDocumentGenerator
{
    public const TEMPLATE_RELATIVE_PATH = 'app/templates/contract.docx';

    public function download(Contract $contract): BinaryFileResponse
    {
        $templatePath = $this->getTemplatePath();

        if (! is_file($templatePath)) {
            throw new RuntimeException(
                'Шаблон договора не найден: '.$templatePath
                .'. Положите DOCX-файл с плейсхолдерами в storage/app/templates/contract.docx.'
            );
        }

        $contract->loadMissing(['client', 'course']);

        $processor = new TemplateProcessor($templatePath);

        foreach ($this->buildValues($contract) as $placeholder => $value) {
            $processor->setValue($placeholder, $value);
        }

        $fileName = $this->buildFileName($contract);
        $outputPath = storage_path('app/tmp/'.$fileName);

        if (! is_dir(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0755, true);
        }

        $processor->saveAs($outputPath);

        return response()
            ->download($outputPath, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])
            ->deleteFileAfterSend();
    }

    protected function getTemplatePath(): string
    {
        return storage_path(self::TEMPLATE_RELATIVE_PATH);
    }

    /**
     * @return array<string, string>
     */
    protected function buildValues(Contract $contract): array
    {
        $client = $contract->client;
        $course = $contract->course;
        $amount = (float) $contract->amount;

        return [
            'contract_number' => (string) $contract->number,
            'contract_date' => $contract->date?->format('d.m.Y') ?? '',
            'client_name' => (string) ($client?->display_name ?? ''),
            'client_inn' => (string) ($client?->inn ?? ''),
            'client_phone' => (string) ($client?->phone ?? ''),
            'client_email' => (string) ($client?->email ?? ''),
            'course_name' => (string) ($course?->name ?? ''),
            'course_hours' => (string) ($course?->hours ?? ''),
            'amount' => number_format($amount, 2, ',', ' '),
            'amount_words' => $this->amountInWords($amount),
            'status' => (string) $contract->status?->label(),
        ];
    }

    protected function buildFileName(Contract $contract): string
    {
        $safeNumber = preg_replace('/[^A-Za-z0-9_\-]/u', '_', (string) $contract->number);

        return 'contract-'.$safeNumber.'.docx';
    }

    /**
     * Простое представление суммы прописью для рублей и копеек.
     * Достаточно для счёта/договора, без сложных склонений.
     */
    protected function amountInWords(float $amount): string
    {
        $rubles = (int) floor($amount);
        $kopecks = (int) round(($amount - $rubles) * 100);

        return sprintf('%d руб. %02d коп.', $rubles, $kopecks);
    }
}
