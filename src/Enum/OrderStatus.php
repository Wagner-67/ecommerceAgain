<?php

namespace App\Enum;

enum OrderStatus: string
{

    case PENDING_PAYMENT = 'pending_payment';

    case PAID = 'paid';

    case COMPLETED = 'completed';

    case FAILED_PAYMENT = 'failed_payment';

    case CANCELLED = 'cancelled';

    case REFUNDED = 'refunded';
    
}
