<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case BKash = 'bkash';
    case Nagad = 'nagad';
    case BankTransfer = 'bank-transfer';
    case Card = 'card';
    case Cash = 'cash';

    public function label(): string
    {
        return match ($this) {
            self::BKash => 'bKash',
            self::Nagad => 'Nagad',
            self::BankTransfer => 'Bank Transfer',
            self::Card => 'Card',
            self::Cash => 'Cash',
        };
    }
}
