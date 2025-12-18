<?php

namespace App\Enum;

enum AddressTypeEnum: string
{
    case DeliveryAddress = 'deliveryAddress';
    case BillingAddress = 'billingAddress';
}