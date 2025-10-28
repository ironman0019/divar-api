<?php

namespace App\Http\Services\Payment;

use App\Models\Payment;
use App\Models\Advertisement\Advertisement;
use App\Models\Advertisement\FeaturedAdvertisement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    private PaymentGatewayService $gatewayService;

    public function __construct(PaymentGatewayService $gatewayService)
    {
        $this->gatewayService = $gatewayService;
    }

    /**
     * Initiate advertisement promotion payment.
     */
    public function initiateAdvertisementPromotion(
        Advertisement $advertisement,
        string $promotionType,
        int $durationDays,
        float $amount,
        string $gateway = 'zarinpal'
    ): array {
        try {
            DB::beginTransaction();

            $user = $advertisement->user;

            // Validate advertisement
            if ($advertisement->status !== 2) {
                throw new \Exception('Advertisement must be active to promote');
            }

            // Validate promotion type
            if (!in_array($promotionType, [Payment::TYPE_LADDER, Payment::TYPE_SPECIAL])) {
                throw new \Exception('Invalid promotion type');
            }

            // Create payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'advertisement_id' => $advertisement->id,
                'amount' => $amount,
                'payment_type' => $promotionType,
                'duration_days' => $durationDays,
                'description' => "پرداخت برای " . ($promotionType === Payment::TYPE_LADDER ? 'نردبان' : 'ویژه') . " کردن آگهی {$advertisement->title}",
                'status' => Payment::STATUS_PENDING,
                'authority' => '',
            ]);

            // Get gateway instance
            $gatewayInstance = $this->gatewayService->getGateway($gateway);

            // Prepare payment data
            $paymentData = [
                'amount' => $amount,
                'description' => $payment->description,
                'mobile' => $user->mobile,
                'email' => $user->email,
            ];

            // Initiate payment with gateway
            $gatewayResponse = $gatewayInstance->initiatePayment($paymentData);

            if (!$gatewayResponse['success']) {
                throw new \Exception('Failed to initiate payment with gateway');
            }

            // Update payment with gateway reference
            $payment->update([
                'authority' => $gatewayResponse['authority'],
                'gateway_response' => $gatewayResponse['gateway_response'],
            ]);

            DB::commit();

            return [
                'success' => true,
                'payment' => $payment,
                'payment_url' => $gatewayResponse['payment_url'],
                'authority' => $gatewayResponse['authority'],
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Advertisement promotion payment initiation failed', [
                'advertisement_id' => $advertisement->id,
                'user_id' => $user->id ?? null,
                'promotion_type' => $promotionType,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Verify and process payment callback.
     */
    public function verifyPayment(array $callbackData, string $gateway = 'zarinpal'): array
    {
        try {
            DB::beginTransaction();

            $authority = $callbackData['Authority'] ?? null;
            if (!$authority) {
                throw new \Exception('Authority parameter is missing');
            }

            // Find payment record
            $payment = Payment::where('authority', $authority)->first();
            if (!$payment) {
                throw new \Exception('Payment record not found');
            }

            // Check if already processed
            if ($payment->isPaid()) {
                return [
                    'success' => true,
                    'payment' => $payment,
                    'message' => 'Payment already processed',
                ];
            }

            // Get gateway instance and verify
            $gatewayInstance = $this->gatewayService->getGateway($gateway);
            $verificationResult = $gatewayInstance->verifyPayment($callbackData);

            // Update payment with verification response
            $payment->update([
                'gateway_response' => array_merge(
                    $payment->gateway_response ?? [],
                    $verificationResult['gateway_response'] ?? []
                ),
            ]);

            if ($verificationResult['success']) {
                // Payment successful - process advertisement promotion
                $this->processSuccessfulPayment($payment, $verificationResult);
            } else {
                // Payment failed
                $payment->markAsFailed($verificationResult['message'] ?? 'Payment verification failed');
            }

            DB::commit();

            return [
                'success' => $verificationResult['success'],
                'payment' => $payment,
                'message' => $verificationResult['message'] ?? 'Payment processed',
                'gateway_transaction_id' => $verificationResult['gateway_transaction_id'] ?? null,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment verification failed', [
                'callback_data' => $callbackData,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Process successful payment for advertisement promotion.
     */
    private function processSuccessfulPayment(Payment $payment, array $verificationResult): void
    {
        $advertisement = $payment->advertisement;

        // Apply promotion based on type
        if ($payment->payment_type === Payment::TYPE_LADDER) {
            $advertisement->update(['is_ladder' => true]);
        } elseif ($payment->payment_type === Payment::TYPE_SPECIAL) {
            $advertisement->update(['is_special' => true]);
            
            // Create featured advertisement record
            FeaturedAdvertisement::create([
                'advertisement_id' => $advertisement->id,
                'payment_id' => $payment->id,
                'type' => $payment->payment_type,
                'expires_at' => now()->addDays($payment->duration_days),
                'is_active' => true,
            ]);
        }

        // Update payment
        $payment->update([
            'ref_id' => $verificationResult['gateway_transaction_id'] ?? null,
        ]);
        $payment->markAsPaid($verificationResult['gateway_transaction_id'] ?? null);
    }

    /**
     * Get payment status.
     */
    public function getPaymentStatus(Payment $payment): array
    {
        return [
            'id' => $payment->id,
            'status' => $payment->status,
            'status_label' => $payment->status_label,
            'amount' => $payment->amount,
            'payment_type' => $payment->payment_type,
            'payment_type_label' => $payment->payment_type_label,
            'duration_days' => $payment->duration_days,
            'ref_id' => $payment->ref_id,
            'created_at' => $payment->created_at,
            'updated_at' => $payment->updated_at,
        ];
    }
}

