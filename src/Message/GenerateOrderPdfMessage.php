<?php

namespace App\Message;

class GenerateOrderPdfMessage
{
    public function __construct(
        public readonly int $orderId,
    ) {}
}