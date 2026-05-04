# PayMongo Webhook Auto-Disable Fix - Complete Guide

## 🚨 Problem: Webhook Keeps Getting Disabled

PayMongo automatically disables webhooks when they receive:
- **4xx or 5xx HTTP status codes**
- **Timeouts (>5 seconds)**
- **Connection errors**
- **SSL/TLS errors**
- **Too many consecutive failures**

---

## ✅ Root Cause Analysis

### Issues Found in Current Implementation:

1. ✅ **Already Fixed**: Always returns 200 OK
2. ✅ **Already Fixed**: Handles all exceptions
3. ✅ **Already Fixed**: Signature verification doesn't fail webhook
4. ⚠️ **Potential Issue**: No timeout protection
5. ⚠️ **Potential Issue**: Synchronous email sending could cause timeout
6. ⚠️ **Potential Issue**: No request time tracking

---

## 🔧 Optimizations Applied

### 1. Timeout Protection
```php
private const TIMEOUT_SECONDS = 3; // Respond within 3s

public function __construct(private BookingPaymentFinalizer $payments)
{
    set_time_limit(self::TIMEOUT_SECONDS);
}
```

### 2. Async Processing for Slow Operations
```php
// Check if approaching timeout
$elapsed = microtime(true) - $startTime;
if ($elapsed > 2.0) {
    // Queue for background processing
    Queue::push(function() use ($groupId, $sessionId, $paymentId, $amountPaid) {
        $this->processPayment($groupId, $sessionId, $paymentId, $amountPaid);
    });
    
    return $this->successResponse('queued for processing');
}
```

### 3. Enhanced Logging
```php
Log::info('[Webhook] ===== PayMongo webhook received =====', [
    'ip'      => $request->ip(),
    'method'  => $request->method(),
    'url'     => $request->fullUrl(),
    'headers' => $request->headers->all(), // Added full headers
]);
```

### 4. Consistent Success Response
```php
private function successResponse(string $note = 'success'): \Illuminate\Http\JsonResponse
{
    return response()->json([
        'ok'   => true,
        'note' => $note,
    ], 200);
}
```

---

## 📋 Implementation Checklist

### Step 1: Replace Webhook Controller

**Option A: Replace Existing File**
```bash
# Backup current file
copy app\Http\Controllers\PaymongoWebhookController.php app\Http\Controllers\PaymongoWebhookController_BACKUP.php

# Replace with optimized version
copy app\Http\Controllers\PaymongoWebhookController_OPTIMIZED.php app\Http\Controllers\PaymongoWebhookController.php
```

**Option B: Manual Update**
Apply the changes from `PaymongoWebhookController_OPTIMIZED.php` to your existing file.

---

### Step 2: Verify Route Configuration

**File**: `routes/web.php`

```php
Route::post('/webhooks/paymongo', [PaymongoWebhookController::class, 'handle'])
    ->name('webhooks.paymongo')
    ->withoutMiddleware([
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        'web',
    ]);
```

✅ **Status**: Already configured correctly

---

### Step 3: Verify CSRF Exemption

**File**: `app/Http/Middleware/VerifyCsrfToken.php`

```php
protected $except = [
    'webhooks/paymongo',
    'api/paymongo/webhook',
];
```

✅ **Status**: Already configured correctly

---

### Step 4: Configure Environment Variables

**File**: `.env`

```env
# PayMongo Configuration
PAYMONGO_PUBLIC_KEY=pk_test_xxxxx
PAYMONGO_SECRET_KEY=sk_test_xxxxx
PAYMONGO_WEBHOOK_SECRET=whsec_xxxxx

# Webhook URL (MUST be HTTPS in production)
PAYMONGO_WEBHOOK_URL=https://yourdomain.com/webhooks/paymongo

# Queue Configuration (for async processing)
QUEUE_CONNECTION=database
```

**Important**: 
- Use `database` queue driver (already configured)
- Run queue worker: `php artisan queue:work`

---

### Step 5: Verify SSL Certificate

**Test SSL**:
```bash
# Test from command line
curl -I https://yourdomain.com/webhooks/paymongo

# Should return:
HTTP/2 200
```

**Common SSL Issues**:
- ❌ Self-signed certificate
- ❌ Expired certificate
- ❌ Certificate chain incomplete
- ❌ Mixed content (HTTP/HTTPS)

**Fix**:
- Use Let's Encrypt (free SSL)
- Use Cloudflare SSL (free)
- Ensure certificate is valid and trusted

---

### Step 6: Test Webhook Endpoint

**Create Test Script**: `test-webhook.php`

```php
<?php

$url = 'https://yourdomain.com/webhooks/paymongo';

$payload = json_encode([
    'data' => [
        'id' => 'evt_test_123',
        'type' => 'event',
        'attributes' => [
            'type' => 'checkout_session.payment.paid',
            'data' => [
                'id' => 'cs_test_123',
                'type' => 'checkout_session',
                'attributes' => [
                    'metadata' => [
                        'group_id' => 'test-group-123',
                    ],
                    'payments' => [
                        'data' => [
                            [
                                'id' => 'pay_test_123',
                                'attributes' => [
                                    'amount' => 500000, // 5000.00 PHP
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
]);

$timestamp = time();
$secret = 'whsec_your_webhook_secret';
$signature = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Paymongo-Signature: t=' . $timestamp . ',te=' . $signature,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";

// Expected:
// HTTP Code: 200
// Response: {"ok":true,"note":"success"}
```

**Run Test**:
```bash
php test-webhook.php
```

**Expected Output**:
```
HTTP Code: 200
Response: {"ok":true,"note":"success"}
```

---

### Step 7: Monitor Webhook Logs

**Check Laravel Logs**:
```bash
tail -f storage/logs/laravel.log | grep "\[Webhook\]"
```

**Expected Log Flow**:
```
[Webhook] ===== PayMongo webhook received =====
[Webhook] Payload received
[Webhook] Signature verified OK
[Webhook] Event type extracted: checkout_session.payment.paid
[Webhook] Identifiers extracted
[Webhook] Payment processed
[Webhook] PAID event handled successfully
```

---

## 🔍 Troubleshooting Guide

### Issue 1: Webhook Still Getting Disabled

**Symptoms**:
- Webhook shows "Disabled" in PayMongo dashboard
- No logs in `storage/logs/laravel.log`

**Diagnosis**:
```bash
# Test if endpoint is reachable
curl -X POST https://yourdomain.com/webhooks/paymongo \
  -H "Content-Type: application/json" \
  -d '{"test":"data"}'

# Should return 200 OK
```

**Possible Causes**:
1. **Firewall blocking PayMongo IPs**
   - Solution: Whitelist PayMongo IPs in firewall
   
2. **Cloudflare blocking requests**
   - Solution: Add firewall rule to allow PayMongo
   
3. **Rate limiting**
   - Solution: Exempt webhook endpoint from rate limiting
   
4. **Server timeout**
   - Solution: Increase PHP max_execution_time

---

### Issue 2: Signature Verification Failing

**Symptoms**:
- Logs show "Signature verification FAILED"
- Webhook still returns 200 OK (correct behavior)

**Diagnosis**:
```bash
# Check if webhook secret is set
php artisan tinker
>>> config('services.paymongo.webhook_secret')
```

**Fix**:
```env
# .env
PAYMONGO_WEBHOOK_SECRET=whsec_your_actual_secret
```

**Get Webhook Secret**:
1. Go to PayMongo Dashboard
2. Developers → Webhooks
3. Click on your webhook
4. Copy "Signing Secret"

---

### Issue 3: Slow Response Times

**Symptoms**:
- Logs show elapsed time > 3 seconds
- Webhook gets disabled intermittently

**Diagnosis**:
```bash
# Check database query performance
tail -f storage/logs/laravel.log | grep "elapsed"
```

**Fix**:
1. **Add Database Indexes**:
```sql
CREATE INDEX idx_bookings_payment_id ON bookings(payment_id);
CREATE INDEX idx_bookings_group_id ON bookings(group_id);
CREATE INDEX idx_boat_bookings_payment_id ON boat_bookings(payment_id);
CREATE INDEX idx_boat_bookings_group_id ON boat_bookings(group_id);
```

2. **Enable Query Caching**:
```php
// In BookingPaymentFinalizer.php
$booking = Cache::remember("booking_{$paymentId}", 60, function() use ($paymentId) {
    return Booking::where('payment_id', $paymentId)->first();
});
```

3. **Use Queue for All Processing**:
```php
// Always queue, never process synchronously
Queue::push(function() use ($groupId, $sessionId, $paymentId, $amountPaid) {
    $this->processPayment($groupId, $sessionId, $paymentId, $amountPaid);
});

return $this->successResponse('queued for processing');
```

---

### Issue 4: SSL/TLS Errors

**Symptoms**:
- PayMongo can't connect to webhook
- Webhook shows "Connection failed" in dashboard

**Diagnosis**:
```bash
# Test SSL from PayMongo's perspective
curl -v https://yourdomain.com/webhooks/paymongo

# Check for:
# - SSL certificate valid
# - No redirect loops
# - No mixed content warnings
```

**Fix**:
1. **Use Cloudflare SSL (Recommended)**:
   - Set SSL mode to "Full (strict)"
   - Enable "Always Use HTTPS"

2. **Use Let's Encrypt**:
```bash
# Install certbot
sudo apt-get install certbot python3-certbot-nginx

# Get certificate
sudo certbot --nginx -d yourdomain.com
```

3. **Verify Certificate Chain**:
```bash
openssl s_client -connect yourdomain.com:443 -showcerts
```

---

### Issue 5: Cloudflare Blocking Webhooks

**Symptoms**:
- Webhook works locally but fails in production
- Cloudflare shows "Challenge" or "Block" in logs

**Fix**:

**Option 1: Firewall Rule (Recommended)**
1. Go to Cloudflare Dashboard
2. Security → WAF → Firewall Rules
3. Create rule:
   - **Name**: Allow PayMongo Webhooks
   - **Field**: URI Path
   - **Operator**: equals
   - **Value**: `/webhooks/paymongo`
   - **Action**: Allow

**Option 2: Page Rule**
1. Go to Cloudflare Dashboard
2. Rules → Page Rules
3. Create rule:
   - **URL**: `yourdomain.com/webhooks/paymongo`
   - **Settings**: 
     - Security Level: Essentially Off
     - Browser Integrity Check: Off
     - Cache Level: Bypass

---

### Issue 6: Queue Not Processing

**Symptoms**:
- Webhook returns 200 OK
- Logs show "queued for processing"
- But bookings never marked as paid

**Diagnosis**:
```bash
# Check if queue worker is running
ps aux | grep "queue:work"

# Check queue jobs
php artisan queue:failed
```

**Fix**:
```bash
# Start queue worker
php artisan queue:work --daemon

# Or use supervisor (production)
sudo apt-get install supervisor

# Create supervisor config: /etc/supervisor/conf.d/laravel-worker.conf
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/storage/logs/worker.log

# Reload supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

---

## 🎯 Production Deployment Checklist

### Pre-Deployment

- [ ] Backup current webhook controller
- [ ] Test webhook endpoint locally
- [ ] Verify SSL certificate is valid
- [ ] Check database indexes exist
- [ ] Verify queue worker is running
- [ ] Test signature verification
- [ ] Review logs for errors

### Deployment

- [ ] Deploy optimized webhook controller
- [ ] Clear application cache: `php artisan cache:clear`
- [ ] Clear config cache: `php artisan config:clear`
- [ ] Restart queue workers: `php artisan queue:restart`
- [ ] Test webhook with PayMongo test event

### Post-Deployment

- [ ] Monitor logs for 24 hours
- [ ] Check webhook status in PayMongo dashboard
- [ ] Verify payments are being processed
- [ ] Check email confirmations are sent
- [ ] Monitor response times

---

## 📊 Monitoring & Alerts

### Setup Log Monitoring

**Create**: `monitor-webhook.sh`

```bash
#!/bin/bash

# Monitor webhook logs
tail -f storage/logs/laravel.log | grep --line-buffered "\[Webhook\]" | while read line; do
    echo "$line"
    
    # Alert on errors
    if echo "$line" | grep -q "ERROR\|FAILED\|CRITICAL"; then
        # Send alert (email, Slack, etc.)
        echo "ALERT: Webhook error detected" | mail -s "Webhook Alert" admin@yourdomain.com
    fi
    
    # Alert on slow responses
    if echo "$line" | grep -q "elapsed.*[3-9]\.[0-9]"; then
        echo "WARNING: Slow webhook response" | mail -s "Webhook Performance" admin@yourdomain.com
    fi
done
```

**Run**:
```bash
chmod +x monitor-webhook.sh
./monitor-webhook.sh &
```

---

### Setup Health Check

**Create**: `routes/web.php`

```php
Route::get('/webhook-health', function () {
    $checks = [
        'webhook_secret_configured' => !empty(config('services.paymongo.webhook_secret')),
        'queue_connection' => config('queue.default'),
        'database_connected' => DB::connection()->getPdo() !== null,
        'ssl_enabled' => request()->secure(),
    ];
    
    $healthy = !in_array(false, $checks, true);
    
    return response()->json([
        'status' => $healthy ? 'healthy' : 'unhealthy',
        'checks' => $checks,
    ], $healthy ? 200 : 500);
});
```

**Monitor**:
```bash
# Check every 5 minutes
*/5 * * * * curl -s https://yourdomain.com/webhook-health | grep -q "healthy" || echo "Webhook unhealthy" | mail -s "Health Check Failed" admin@yourdomain.com
```

---

## 🔐 Security Best Practices

### 1. Always Verify Signature
```php
if (!$this->verifySignature($rawPayload, $header)) {
    Log::warning('[Webhook] Signature verification FAILED');
    // Still return 200 OK, but don't process
    return $this->successResponse('signature verification failed');
}
```

### 2. Rate Limiting (Optional)
```php
// In routes/web.php
Route::post('/webhooks/paymongo', [PaymongoWebhookController::class, 'handle'])
    ->middleware('throttle:60,1'); // 60 requests per minute
```

### 3. IP Whitelisting (Optional)
```php
// In PaymongoWebhookController.php
private const PAYMONGO_IPS = [
    '52.77.235.50',
    '52.77.197.23',
    // Add PayMongo IPs
];

public function handle(Request $request)
{
    if (!in_array($request->ip(), self::PAYMONGO_IPS)) {
        Log::warning('[Webhook] Request from unauthorized IP', ['ip' => $request->ip()]);
        return $this->successResponse('unauthorized ip');
    }
    
    // Continue processing...
}
```

---

## 📈 Performance Optimization

### 1. Database Indexes
```sql
-- Add indexes for fast lookups
CREATE INDEX idx_bookings_payment_id ON bookings(payment_id);
CREATE INDEX idx_bookings_group_id ON bookings(group_id);
CREATE INDEX idx_bookings_payment_status ON bookings(payment_status);
CREATE INDEX idx_boat_bookings_payment_id ON boat_bookings(payment_id);
CREATE INDEX idx_boat_bookings_group_id ON boat_bookings(group_id);
CREATE INDEX idx_boat_bookings_payment_status ON boat_bookings(payment_status);
```

### 2. Query Optimization
```php
// Use select() to fetch only needed columns
$booking = Booking::select('id', 'group_id', 'payment_status')
    ->where('payment_id', $sessionId)
    ->first();
```

### 3. Cache Configuration
```php
// Cache webhook secret (read once, cache forever)
$secret = Cache::rememberForever('paymongo_webhook_secret', function() {
    return config('services.paymongo.webhook_secret');
});
```

---

## 🆘 Emergency Recovery

### If Webhook Gets Disabled

**Step 1: Check PayMongo Dashboard**
1. Go to Developers → Webhooks
2. Check webhook status
3. Review error logs

**Step 2: Re-enable Webhook**
1. Click "Enable" button
2. Test with sample event
3. Monitor logs

**Step 3: Verify Endpoint**
```bash
curl -X POST https://yourdomain.com/webhooks/paymongo \
  -H "Content-Type: application/json" \
  -d '{"test":"data"}'

# Must return 200 OK
```

**Step 4: Check Recent Logs**
```bash
tail -100 storage/logs/laravel.log | grep "\[Webhook\]"
```

**Step 5: Fix Root Cause**
- Review error logs
- Apply fixes from troubleshooting guide
- Test thoroughly before re-enabling

---

## 📞 Support Resources

### PayMongo Support
- **Email**: support@paymongo.com
- **Docs**: https://developers.paymongo.com/docs/webhooks
- **Status**: https://status.paymongo.com

### Laravel Resources
- **Docs**: https://laravel.com/docs/queues
- **Forum**: https://laracasts.com/discuss

---

## ✅ Final Verification

Run this checklist after deployment:

```bash
# 1. Test webhook endpoint
curl -X POST https://yourdomain.com/webhooks/paymongo \
  -H "Content-Type: application/json" \
  -d '{"test":"data"}'
# Expected: HTTP 200 OK

# 2. Check SSL
curl -I https://yourdomain.com/webhooks/paymongo
# Expected: HTTP/2 200

# 3. Verify queue worker
ps aux | grep "queue:work"
# Expected: Process running

# 4. Check logs
tail -f storage/logs/laravel.log | grep "\[Webhook\]"
# Expected: No errors

# 5. Test with PayMongo
# Go to PayMongo Dashboard → Webhooks → Send Test Event
# Expected: Success
```

---

**Status**: ✅ Production Ready
**Last Updated**: 2025
**Maintainer**: Development Team
