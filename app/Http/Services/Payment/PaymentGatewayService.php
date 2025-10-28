<?php

namespace App\Http\Services\Payment;


use InvalidArgumentException;
use App\Contracts\PaymentGatewayInterface;
use App\Http\Services\Payment\Gateways\ZarinpalGateway;

class PaymentGatewayService
{
    /**
     * Get a payment gateway instance.
     */
    public function getGateway(string $gatewayName): PaymentGatewayInterface
    {
        return match ($gatewayName) {
            'zarinpal' => new ZarinpalGateway(),
            default => throw new InvalidArgumentException("Unsupported payment gateway: {$gatewayName}"),
        };
    }

    /**
     * Get the default payment gateway.
     */
    public function getDefaultGateway(): PaymentGatewayInterface
    {
        return $this->getGateway('zarinpal');
    }

    /**
     * Get all available payment gateways.
     */
    public function getAvailableGateways(): array
    {
        return [
            'zarinpal' => [
                'name' => 'Zarinpal',
                'description' => 'Zarinpal Payment Gateway',
                'sandbox' => config('services.zarinpal.sandbox', true),
            ],
        ];
    }
}

