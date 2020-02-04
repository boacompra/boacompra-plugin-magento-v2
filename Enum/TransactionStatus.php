<?php

namespace Uol\BoaCompra\Enum;

final class TransactionStatus
{
    const CANCELLED = 'CANCELLED';
    const COMPLETE = 'COMPLETE';
    const CHARGEBACK = 'CHARGEBACK';
    const EXPIRED = 'EXPIRED';
    const NOT_PAID = 'NOT-PAID';
    const PENDING = 'PENDING';
    const REFUNDED = 'REFUNDED';
    const UNDER_REVIEW = 'UNDER-REVIEW';
}
