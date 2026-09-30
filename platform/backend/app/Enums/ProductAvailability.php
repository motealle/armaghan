<?php

namespace App\Enums;

enum ProductAvailability: string
{
    case Available = 'available';
    case Unavailable = 'unavailable';
    case MadeToOrder = 'made_to_order';
}
