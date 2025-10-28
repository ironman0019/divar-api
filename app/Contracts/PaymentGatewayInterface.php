<?php

namespace App\Contracts;

interface PaymentGatewayInterface
{
    /**
     * Initiate a payment request with the gateway.
     *
     * @param array $data Payment data including amount, description, etc.
     * @return array Response containing payment URL and reference
     * @throws \Exception If payment initiation fails
     */
    public function initiatePayment(array $data): array;

    /**
     * Verify a payment callback from the gateway.
     *
     * @param array $callbackData Callback data from gateway
     * @return array Verification result with payment status
     * @throws \Exception If verification fails
     */
    public function verifyPayment(array $callbackData): array;

    /**
     * Get the gateway name.
     *
     * @return string
     */
    public function getGatewayName(): string;

    /**
     * Check if the gateway is in sandbox mode.
     *
     * @return bool
     */
    public function isSandbox(): bool;

    /**
     * Get the callback URL for this gateway.
     *
     * @return string
     */
    public function getCallbackUrl(): string;
}

