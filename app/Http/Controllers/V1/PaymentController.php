<?php

namespace App\Http\Controllers\V1;

use App\Traits\HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Services\Payment\PaymentService;
use App\Http\Services\AdvertisementPromotionService;
use App\Models\Advertisement\Advertisement;
use App\Models\Advertisement\PromotionPrice;
use App\Http\Resources\V1\Payment\PromotionPriceResource;
use App\Http\Resources\V1\Payment\PaymentResource;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    use HttpResponse;

    protected PaymentService $paymentService;
    protected AdvertisementPromotionService $promotionService;

    public function __construct(
        PaymentService $paymentService,
        AdvertisementPromotionService $promotionService
    ) {
        $this->paymentService = $paymentService;
        $this->promotionService = $promotionService;
    }

    /**
     * Get available promotion prices.
     */
    public function getPromotionPrices(): JsonResponse
    {
        try {
            $promotionPrices = PromotionPrice::active()
                ->orderBy('type')
                ->orderBy('duration_days')
                ->get();

            return $this->success([
                'promotion_prices' => PromotionPriceResource::collection($promotionPrices),
            ], __('messages.payments.prices_retrieved'));

        } catch (\Exception $e) {
            return $this->failed(null, __('messages.errors.server_error'), 500);
        }
    }

    /**
     * Get promotion options for a specific advertisement.
     */
    public function getAdvertisementPromotions(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'advertisement_id' => 'required|integer|exists:advertisements,id',
            ]);

            if ($validator->fails()) {
                return $this->failed($validator->errors(), __('messages.errors.validation_failed'), 422);
            }

            $advertisementId = $request->advertisement_id;
            $user = auth('api')->user();

            // Check if advertisement belongs to user
            $advertisement = Advertisement::where('id', $advertisementId)
                ->where('user_id', $user->id)
                ->first();

            if (!$advertisement) {
                return $this->failed(null, __('messages.advertisements.not_found'), 404);
            }

            $promotions = $this->promotionService->getAvailablePromotions($advertisementId);

            return $this->success($promotions, __('messages.payments.promotions_retrieved'));

        } catch (\Exception $e) {
            return $this->failed(null, $e->getMessage(), 400);
        }
    }

    /**
     * Initiate payment for advertisement promotion.
     */
    public function initiatePromotion(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'advertisement_id' => 'required|integer|exists:advertisements,id',
                'promotion_type' => 'required|in:ladder,special',
                'duration_days' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return $this->failed($validator->errors(), __('messages.errors.validation_failed'), 422);
            }

            $user = auth('api')->user();
            $advertisementId = $request->advertisement_id;
            $promotionType = $request->promotion_type;
            $durationDays = $request->duration_days;

            // Check if advertisement belongs to user
            $advertisement = Advertisement::where('id', $advertisementId)
                ->where('user_id', $user->id)
                ->first();

            if (!$advertisement) {
                return $this->failed(null, __('messages.advertisements.not_found'), 404);
            }

            // Check if advertisement can be promoted
            if (!$this->promotionService->canPromoteAdvertisement($advertisementId)) {
                return $this->failed(null, __('messages.payments.advertisement_not_active'), 400);
            }

            // Get promotion price
            $promotionPrice = $this->promotionService->getPromotionPrice($promotionType, $durationDays);
            if (!$promotionPrice) {
                return $this->failed(null, __('messages.payments.price_not_found'), 404);
            }

            // Initiate payment
            $result = $this->paymentService->initiateAdvertisementPromotion(
                $advertisement,
                $promotionType,
                $durationDays,
                $promotionPrice->price
            );

            return $this->success([
                'payment' => new PaymentResource($result['payment']),
                'payment_url' => $result['payment_url'],
                'authority' => $result['authority'],
            ], __('messages.payments.initiated'));

        } catch (\Exception $e) {
            return $this->failed(null, $e->getMessage(), 400);
        }
    }

    /**
     * Verify payment callback from gateway.
     */
    public function verifyCallback(Request $request): JsonResponse
    {
        try {
            $callbackData = $request->all();
            
            $result = $this->paymentService->verifyPayment($callbackData);

            if ($result['success']) {
                return $this->success([
                    'payment' => new PaymentResource($result['payment']),
                    'message' => $result['message'],
                    'gateway_transaction_id' => $result['gateway_transaction_id'],
                ], __('messages.payments.verified_success'));
            } else {
                return $this->failed([
                    'payment' => new PaymentResource($result['payment']),
                    'message' => $result['message'],
                ], __('messages.payments.verified_failed'), 400);
            }

        } catch (\Exception $e) {
            return $this->failed(null, $e->getMessage(), 400);
        }
    }

    /**
     * Get payment status.
     */
    public function getPaymentStatus(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'authority' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->failed($validator->errors(), __('messages.errors.validation_failed'), 422);
            }

            $authority = $request->authority;
            $payment = \App\Models\Payment::where('authority', $authority)->first();

            if (!$payment) {
                return $this->failed(null, __('messages.payments.not_found'), 404);
            }

            $status = [
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

            return $this->success($status, __('messages.payments.status_retrieved'));

        } catch (\Exception $e) {
            return $this->failed(null, $e->getMessage(), 400);
        }
    }

    /**
     * Get user's payment history.
     */
    public function getPaymentHistory(Request $request): JsonResponse
    {
        try {
            $user = auth('api')->user();
            
            $payments = \App\Models\Payment::where('user_id', $user->id)
                ->with(['advertisement'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return $this->success([
                'data' => PaymentResource::collection($payments->items()),
                'pagination' => [
                    'current_page' => $payments->currentPage(),
                    'last_page' => $payments->lastPage(),
                    'per_page' => $payments->perPage(),
                    'total' => $payments->total(),
                    'from' => $payments->firstItem(),
                    'to' => $payments->lastItem(),
                ],
            ], __('messages.payments.history_retrieved'));

        } catch (\Exception $e) {
            return $this->failed(null, $e->getMessage(), 400);
        }
    }
}
