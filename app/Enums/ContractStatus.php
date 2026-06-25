<?php

namespace App\Enums;

enum ContractStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Studying = 'studying';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Ожидает оплаты',
            self::Paid => 'Оплачен',
            self::Studying => 'Обучается',
            self::Completed => 'Завершён',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Paid => 'info',
            self::Studying => 'primary',
            self::Completed => 'success',
        };
    }
}
