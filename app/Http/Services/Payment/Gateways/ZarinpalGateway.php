<?php

namespace App\Http\Services\Payment\Gateways;

use App\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZarinpalGateway implements PaymentGatewayInterface
{
    private string $merchantId;
    private bool $sandbox;
    private string $baseUrl;
    private string $paymentUrl;
    private string $callbackUrl;

    public function __construct()
    {
        $config = config('services.zarinpal');
        $this->merchantId = $config['merchant_id'];
        $this->sandbox = $config['sandbox'];
        $this->callbackUrl = $config['callback_url'];
        
        if ($this->sandbox) {
            $this->baseUrl = $config['sandbox_url'];
            $this->paymentUrl = $config['payment_url']['sandbox'];
        } else {
            $this->baseUrl = $config['production_url'];
            $this->paymentUrl = $config['payment_url']['production'];
        }
    }

    /**
     * Initiate a payment request with Zarinpal.
     */
    public function initiatePayment(array $data): array
    {
        try {
            $requestData = [
                'merchant_id' => $this->merchantId,
                'amount' => $data['amount'] * 10, // Convert to Rials (Zarinpal expects Rials)
                'description' => $data['description'] ?? 'Payment',
                'callback_url' => $this->callbackUrl,
                'metadata' => [
                    'mobile' => $data['mobile'] ?? null,
                    'email' => $data['email'] ?? null,
                ],
            ];

            // Remove null values from metadata
            $requestData['metadata'] = array_filter($requestData['metadata']);

            Log::info('Zarinpal payment request', [
                'data' => $requestData,
                'sandbox' => $this->sandbox,
            ]);

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($this->baseUrl . 'request.json', $requestData);

            $responseData = $response->json();

            Log::info('Zarinpal payment response', [
                'status' => $response->status(),
                'data' => $responseData,
            ]);

            if ($response->successful() && isset($responseData['data']['authority'])) {
                $authority = $responseData['data']['authority'];
                $paymentUrl = $this->paymentUrl . $authority;

                return [
                    'success' => true,
                    'authority' => $authority,
                    'payment_url' => $paymentUrl,
                    'gateway_response' => $responseData,
                ];
            }

            throw new \Exception(
                $responseData['errors']['message'] ?? 'Failed to initiate payment',
                $responseData['errors']['code'] ?? 0
            );

        } catch (\Exception $e) {
            Log::error('Zarinpal payment initiation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            throw new \Exception('Payment initiation failed: ' . $e->getMessage());
        }
    }

    /**
     * Verify a payment callback from Zarinpal.
     */
    public function verifyPayment(array $callbackData): array
    {
        try {
            $authority = $callbackData['Authority'] ?? null;
            $status = $callbackData['Status'] ?? null;

            if (!$authority) {
                throw new \Exception('Authority parameter is missing');
            }

            // If status is not OK, payment was not successful
            if ($status !== 'OK') {
                return [
                    'success' => false,
                    'status' => 'failed',
                    'message' => 'Payment was not completed',
                    'authority' => $authority,
                    'gateway_response' => $callbackData,
                ];
            }

            // Get payment amount from database using authority
            $payment = \App\Models\Payment::where('authority', $authority)->first();
            
            if (!$payment) {
                throw new \Exception('Payment record not found');
            }

            $verifyData = [
                'merchant_id' => $this->merchantId,
                'amount' => $payment->amount * 10, // Convert to Rials
                'authority' => $authority,
            ];

            Log::info('Zarinpal payment verification request', [
                'data' => $verifyData,
                'sandbox' => $this->sandbox,
            ]);

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($this->baseUrl . 'verify.json', $verifyData);

            $responseData = $response->json();

            Log::info('Zarinpal payment verification response', [
                'status' => $response->status(),
                'data' => $responseData,
            ]);

            if ($response->successful() && isset($responseData['data']['code']) && $responseData['data']['code'] === 100) {
                return [
                    'success' => true,
                    'status' => 'completed',
                    'authority' => $authority,
                    'ref_id' => $responseData['data']['ref_id'],
                    'gateway_transaction_id' => $responseData['data']['ref_id'],
                    'gateway_response' => $responseData,
                ];
            }

            $errorMessage = $responseData['errors']['message'] ?? 'Payment verification failed';
            $errorCode = $responseData['errors']['code'] ?? 0;

            return [
                'success' => false,
                'status' => 'failed',
                'message' => $errorMessage,
                'error_code' => $errorCode,
                'authority' => $authority,
                'gateway_response' => $responseData,
            ];

        } catch (\Exception $e) {
            Log::error('Zarinpal payment verification failed', [
                'error' => $e->getMessage(),
                'callback_data' => $callbackData,
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Payment verification failed: ' . $e->getMessage(),
                'authority' => $callbackData['Authority'] ?? null,
                'gateway_response' => $callbackData,
            ];
        }
    }

    /**
     * Get the gateway name.
     */
    public function getGatewayName(): string
    {
        return 'zarinpal';
    }

    /**
     * Check if the gateway is in sandbox mode.
     */
    public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    /**
     * Get the callback URL for this gateway.
     */
    public function getCallbackUrl(): string
    {
        return $this->callbackUrl;
    }
}

