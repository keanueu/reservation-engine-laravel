# 🚨 WEBHOOK QUICK FIX - DO THIS NOW

## ⚡ IMMEDIATE ACTIONS (5 MINUTES)

### 1️⃣ RE-ENABLE WEBHOOK IN PAYMONGO

**Go to:** https://dashboard.paymongo.com/developers/webhooks

**Click:** "Enable" button on your disabled webhook

**OR create new webhook:**
- URL: `https://stanchly-undeepened-jaymie.ngrok-free.dev/webhooks/paymongo`
- Events: `checkout_session.payment.paid`, `payment.paid`, `checkout_session.payment.failed`

---

### 2️⃣ START QUEUE WORKER

```bash
# Double-click this file:
start-queue-worker.bat

# OR run manually:
php artisan queue:work --queue=mail,default
```

---

### 3️⃣ TEST IT WORKS

Make a test payment and check:
- ✅ Booking status = "paid"
- ✅ Email received
- ✅ No error in logs

---

## 🛡️ WHY IT WAS DISABLING

**OLD CODE (BAD):**
```php
// Returns 400 → PayMongo disables webhook ❌
return response()->json(['error' => 'failed'], 400);
```

**NEW CODE (FIXED):**
```php
// Returns 200 → PayMongo keeps webhook enabled ✅
return response()->json(['ok' => true, 'note' => 'error logged']);
```

---

## 📋 DAILY CHECKLIST

- [ ] Queue worker is running
- [ ] Webhook shows "enabled" in PayMongo Dashboard
- [ ] Test payment works
- [ ] No disable emails from PayMongo

---

## 🆘 IF WEBHOOK DISABLES AGAIN

1. **Check logs:**
   ```bash
   type storage\logs\laravel.log | findstr /i "webhook"
   ```

2. **Clear cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

3. **Re-enable webhook:**
   - PayMongo Dashboard → Webhooks → Enable

4. **Contact PayMongo support:**
   - Email: developers@paymongo.com
   - Share your webhook logs

---

## ✅ VERIFICATION

**Webhook is working when:**
- Status = "enabled" in PayMongo Dashboard
- Payments update to "paid" automatically
- Emails send within 30 seconds
- No daily disable emails

---

**Read full guide:** `WEBHOOK_REACTIVATION_GUIDE.md`
