<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProductAvailability: string implements HasLabel
{
    case Available = 'available';
    case Unavailable = 'unavailable';
    case MadeToOrder = 'made_to_order';

    public function getLabel(): string
    {
        return match ($this) {
            self::Available => 'موجود',
            self::Unavailable => 'ناموجود',
            self::MadeToOrder => 'تولیدپذیر',
        };
    }
}
