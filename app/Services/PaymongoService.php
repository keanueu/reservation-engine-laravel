<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaymongoService
{
    protected $baseUrl = 'https://api.paymongo.com/v1';

    public function __construct()
    {
        // Nothing needed here; we read PAYMONGO_SECRET from env each request
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
    public function createLink(int $amountInCents, $arg2 = null, $arg3 = null, string $arg4 = 'PHP'): array
    {
        $secret = env('PAYMONGO_SECRET');

        if (empty($secret)) {
            return [
                'success' => false,
                'message' => 'PAYMONGO_SECRET not configured',
                'raw' => null,
            ];
        }

        $url = $this->baseUrl . '/links';

        // Normalize args
        $description = '';
        $currency = 'PHP';
        $metadata = [];

        if (is_array($arg2) && $arg3 === null) {
            // createLink(amount, metadata)
            $metadata = $arg2;
        } elseif (is_string($arg2)) {
            // If arg2 looks like a 3-letter currency code (e.g. PHP), treat it as currency
            if (strlen($arg2) === 3 && strtoupper($arg2) === $arg2) {
                $currency = $arg2;
                if (is_array($arg3)) $metadata = $arg3;
            } else {
                // Treat as description
                $description = $arg2;
                if (is_array($arg3)) $metadata = $arg3;
                if (!empty($arg4)) $currency = $arg4;
            }
        }

        // If description not provided, try to use metadata.description when available
        if (empty($description) && !empty($metadata) && is_array($metadata) && !empty($metadata['description'])) {
            $description = $metadata['description'];
        }

        $body = [
            'data' => [
                'attributes' => array_filter([
                    'amount' => $amountInCents,
                    'currency' => $currency,
                    'description' => $description ?: null,
                    'metadata' => !empty($metadata) ? $metadata : null,
                ], function ($v) { return $v !== null; }),
            ],
        ];

        try {
            $response = Http::withBasicAuth($secret, '')
                ->acceptJson()
                ->post($url, $body);

            $json = $response->json();

            if ($response->successful() && isset($json['data']['id'])) {
                return [
                    'success' => true,
                    'link_id' => $json['data']['id'],
                    'checkout_url' => $json['data']['attributes']['checkout_url'] ?? null,
                    'raw' => $json,
                ];
            }

            return [
                'success' => false,
                'message' => $json['errors'] ?? ($json['message'] ?? 'unknown error'),
                'raw' => $json,
                'status_code' => $response->status(),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'raw' => null,
            ];
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
        $secret = env('PAYMONGO_SECRET');
        if (empty($secret)) return null;

        $url = $this->baseUrl . '/links/' . $linkId;
        try {
            $response = Http::withBasicAuth($secret, '')->acceptJson()->get($url);
            return $response->json();
        } catch (\Exception $e) {
            return null;
        }
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
        $secret = env('PAYMONGO_SECRET');

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
