<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymongoService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.paymongo.base', 'https://api.paymongo.com/v1'), '/');
    }

    /**
     * Create a PayMongo Payment Link.
     *
     * Flexible signature to support existing callers:
     *  - createLink(amountInCents, description = '', metadata = [], currency = 'PHP')
     *  - createLink(amountInCents, currency = 'PHP', metadata = [])  (legacy call from extensions)
     *
     * @param int $amountInCents
     * @param mixed $arg2 (description|string or currency code)
     * @param array|null $arg3 metadata
     * @param string $arg4 currency code
     * @return array
     */
    public function createLink(int $amountInCents, $arg2 = null, $arg3 = null, ?string $successUrl = null, ?string $cancelUrl = null): array
    {
        $secret = config('services.paymongo.key');

        if (empty($secret)) {
            return ['success' => false, 'message' => 'PAYMONGO_SECRET not configured', 'raw' => null];
        }

        $url = $this->baseUrl . '/links';

        // Normalize args (preserve backward compatibility)
        $description = '';
        $currency    = 'PHP';
        $metadata    = [];

        if (is_array($arg2) && $arg3 === null) {
            $metadata = $arg2;
        } elseif (is_string($arg2)) {
            if (strlen($arg2) === 3 && strtoupper($arg2) === $arg2) {
                $currency = $arg2;
                if (is_array($arg3)) $metadata = $arg3;
            } else {
                $description = $arg2;
                if (is_array($arg3)) $metadata = $arg3;
            }
        }

        if (empty($description) && !empty($metadata['description'])) {
            $description = $metadata['description'];
        }

        $attributes = [
            'amount'      => $amountInCents,
            'currency'    => $currency,
            'description' => $description ?: null,
            'metadata'    => !empty($metadata) ? $metadata : null,
        ];

        // Add redirect URLs when provided
        if ($successUrl) {
            $attributes['redirect'] = [
                'success' => $successUrl,
                'failed'  => $cancelUrl ?? $successUrl,
            ];
        }

        $body = [
            'data' => [
                'attributes' => array_filter($attributes, fn($v) => $v !== null),
            ],
        ];

        try {
            $response = Http::withBasicAuth($secret, '')
                ->acceptJson()
                ->post($url, $body);

            $json = $response->json();

            if ($response->successful() && isset($json['data']['id'])) {
                return [
                    'success'      => true,
                    'link_id'      => $json['data']['id'],
                    'checkout_url' => $json['data']['attributes']['checkout_url'] ?? null,
                    'raw'          => $json,
                ];
            }

            return [
                'success'     => false,
                'message'     => $json['errors'] ?? ($json['message'] ?? 'unknown error'),
                'raw'         => $json,
                'status_code' => $response->status(),
            ];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage(), 'raw' => null];
        }
    }

    /**
     * Create a PayMongo Checkout Session.
     *
     * Uses the /checkout_sessions endpoint which natively supports
     * success_url and cancel_url inside data.attributes — this is
     * what shows the 'Return to Merchant' button on the PayMongo page.
     *
     * @param int    $amountInCents
     * @param string $description
     * @param array  $metadata
     * @param string $successUrl
     * @param string $cancelUrl
     * @return array
     */
    public function createCheckoutSession(
        int    $amountInCents,
        string $description,
        array  $metadata,
        string $successUrl,
        string $cancelUrl
    ): array {
        $secret = config('services.paymongo.key');

        if (empty($secret)) {
            return ['success' => false, 'message' => 'PAYMONGO_SECRET not configured', 'raw' => null];
        }

        $url = $this->baseUrl . '/checkout_sessions';

        // Ensure group_id is always at the top level of metadata
        // so the webhook can find it at data.attributes.data.attributes.metadata.group_id
        $safeMetadata = array_merge($metadata, [
            'group_id' => $metadata['group_id'] ?? null,
        ]);

        $body = [
            'data' => [
                'attributes' => [
                    'cancel_url'           => $cancelUrl,
                    'description'          => $description,
                    'line_items'           => [
                        [
                            'currency' => 'PHP',
                            'amount'   => $amountInCents,
                            'name'     => $description,
                            'quantity' => 1,
                        ],
                    ],
                    'metadata'             => $safeMetadata,
                    'payment_method_types' => ['card', 'gcash', 'paymaya', 'grab_pay'],
                    'send_email_receipt'   => true,
                    'show_description'     => true,
                    'show_line_items'      => true,
                    'success_url'          => $successUrl,
                ],
            ],
        ];

        Log::info('PayMongo createCheckoutSession request', [
            'url'      => $url,
            'group_id' => $safeMetadata['group_id'] ?? null,
            'amount'   => $amountInCents,
        ]);

        try {
            $response = Http::withBasicAuth($secret, '')
                ->acceptJson()
                ->post($url, $body);

            $json = $response->json();

            Log::info('PayMongo createCheckoutSession response', [
                'status' => $response->status(),
                'id'     => $json['data']['id'] ?? null,
                'errors' => $json['errors'] ?? null,
            ]);

            if ($response->successful() && isset($json['data']['id'])) {
                return [
                    'success'      => true,
                    'session_id'   => $json['data']['id'],
                    'checkout_url' => $json['data']['attributes']['checkout_url'] ?? null,
                    'raw'          => $json,
                ];
            }

            return [
                'success'     => false,
                'message'     => $json['errors'] ?? ($json['message'] ?? 'unknown error'),
                'raw'         => $json,
                'status_code' => $response->status(),
            ];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage(), 'raw' => null];
        }
    }

    /**
     * Get a PayMongo Link resource by id.
     * Used to inspect link/payment status.
     *
     * @param string $linkId
     * @return array|null
     */
    public function getLink(string $linkId): ?array
    {
        $secret = config('services.paymongo.key');
        if (empty($secret)) return null;

        $url = $this->baseUrl . '/links/' . $linkId;
        try {
            $response = Http::withBasicAuth($secret, '')->acceptJson()->get($url);
            return $response->json();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getCheckoutSession(string $sessionId): array
    {
        $secret = config('services.paymongo.key');

        if (empty($secret)) {
            return ['success' => false, 'message' => 'PAYMONGO_SECRET not configured', 'raw' => null];
        }

        $url = $this->baseUrl . '/checkout_sessions/' . $sessionId;

        try {
            $response = Http::withBasicAuth($secret, '')
                ->acceptJson()
                ->get($url);

            $json = $response->json();

            Log::info('PayMongo getCheckoutSession response', [
                'status' => $response->status(),
                'session_id' => $sessionId,
                'payment_status' => data_get($json, 'data.attributes.payment_intent.attributes.status'),
                'payments_count' => count($this->paymentsFromCheckoutSession($json)),
                'errors' => $json['errors'] ?? null,
            ]);

            return [
                'success' => $response->successful() && isset($json['data']['id']),
                'message' => $json['errors'] ?? ($json['message'] ?? null),
                'raw' => $json,
                'status_code' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('PayMongo getCheckoutSession failed', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => $e->getMessage(), 'raw' => null];
        }
    }

    public function paidCheckoutSessionDetails(array $checkoutSession): ?array
    {
        $attributes = data_get($checkoutSession, 'data.attributes', data_get($checkoutSession, 'attributes', []));
        $payments = $this->paymentsFromCheckoutSession($checkoutSession);
        $payment = collect($payments)->first(function ($payment) {
            return data_get($payment, 'attributes.status') === 'paid';
        }) ?: ($payments[0] ?? null);

        $paymentStatus = data_get($payment, 'attributes.status');
        $intentStatus = data_get($attributes, 'payment_intent.attributes.status');

        if ($paymentStatus !== 'paid' && $intentStatus !== 'succeeded') {
            return null;
        }

        $paymentId = data_get($payment, 'id')
            ?: data_get($attributes, 'payment_intent.attributes.payments.0.id')
            ?: data_get($attributes, 'payment_intent.attributes.payments.data.0.id');

        $amountCentavos = (int) (
            data_get($payment, 'attributes.amount')
            ?: data_get($attributes, 'payment_intent.attributes.amount')
            ?: data_get($attributes, 'line_items.0.amount', 0)
        );

        return [
            'payment_id' => $paymentId,
            'amount_php' => $amountCentavos > 0 ? round($amountCentavos / 100, 2) : 0.0,
            'payment_status' => $paymentStatus,
            'intent_status' => $intentStatus,
        ];
    }

    private function paymentsFromCheckoutSession(array $checkoutSession): array
    {
        $attributes = data_get($checkoutSession, 'data.attributes', data_get($checkoutSession, 'attributes', []));

        $payments = data_get($attributes, 'payments', []);
        if (is_array($payments) && array_is_list($payments)) {
            return $payments;
        }

        $payments = data_get($attributes, 'payments.data', []);
        if (is_array($payments) && array_is_list($payments)) {
            return $payments;
        }

        $payments = data_get($attributes, 'payment_intent.attributes.payments', []);
        if (is_array($payments) && array_is_list($payments)) {
            return $payments;
        }

        $payments = data_get($attributes, 'payment_intent.attributes.payments.data', []);

        return is_array($payments) ? $payments : [];
    }

    /**
     * Refund a payment via PayMongo.
     *
     * @param string $paymentId PayMongo payment id (e.g. pay_xxx)
     * @param int $amountInCents Amount in cents (integer)
     * @param string|null $reason
     * @return array
     */
    public function refundPayment(string $paymentId, int $amountInCents, ?string $reason = null): array
    {
        $secret = config('services.paymongo.key');

        if (empty($secret)) {
            return [
                'success' => false,
                'message' => 'PAYMONGO_SECRET not configured',
                'raw' => null,
            ];
        }

        $url = $this->baseUrl . '/refunds';

        $body = [
            'data' => [
                'attributes' => [
                    'amount' => $amountInCents,
                    // PayMongo expects `payment_id` as the attribute name for refunds
                    'payment_id' => $paymentId,
                ],
            ],
        ];

        // Only include reason when it matches one of PayMongo's accepted reason codes.
        // Avoid sending arbitrary free-text reasons which PayMongo will reject.
        $allowedReasons = [
            'duplicate',
            'fraud',
            'requested_by_customer',
            'processing_error',
        ];

        if (!empty($reason) && in_array($reason, $allowedReasons, true)) {
            $body['data']['attributes']['reason'] = $reason;
        }

        try {
            $response = Http::withBasicAuth($secret, '')
                ->acceptJson()
                ->post($url, $body);

            $status = $response->status();
            $json = $response->json();

            if ($response->successful() && isset($json['data']['id'])) {
                return [
                    'success' => true,
                    'refund_id' => $json['data']['id'] ?? null,
                    'status' => $json['data']['attributes']['status'] ?? null,
                    'raw' => $json,
                ];
            }

            return [
                'success' => false,
                'message' => $json['errors'] ?? ($json['message'] ?? 'unknown error'),
                'raw' => $json,
                'status_code' => $status,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'raw' => null,
            ];
        }
    }
}
