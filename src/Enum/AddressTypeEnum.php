<?php

namespace App\Enum;

enum AddressTypeEnum: string
{
    case DeliveryAddress = 'deliveryAddress';
    case BillingAddress = 'billingAddress';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function isValid(string $value): bool
    {
        return in_array($value, self::values(), true);
    }

    public function getLabel(): string
    {
        return match($this) {

            self::BILLING => 'Billing Address',
            self::DeliveryAddress => 'Delivery Address',

        };
    }
}