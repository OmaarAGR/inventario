<?php

namespace App\Domain\Invoice\Enums;

enum InvoiceStatus: string
{
    case Draft = 'draft';
    case Paid = 'paid';
    case Cancelled = 'cancelled';
}
