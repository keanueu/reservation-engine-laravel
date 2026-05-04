# 🚀 QUICK START - FIX PAYMENT & EMAIL NOW

## ⏱️ 5-MINUTE FIX

### 1️⃣ START QUEUE WORKER (RIGHT NOW!)

**Double-click this file:**
```
start-queue-worker.bat
```

**OR open Command Prompt and run:**
```bash
cd c:\xampp\htdocs\boatxpress-laravel
php artisan queue:work --queue=mail,default
```

✅ **KEEP THIS WINDOW OPEN!** Closing it stops email processing.

---

### 2️⃣ REGISTER WEBHOOK IN PAYMONGO

1. **Open:** https://dashboard.paymongo.com/
2. **Login** with your PayMongo account
3. **Navigate:** Developers → Webhooks
4. **Click:** "Add Webhook" or "Create Webhook"
5. **Fill in:**
   - **URL:** `https://stanchly-undeepened-jaymie.ngrok-free.dev/webhooks/paymongo`
   - **Events:** Select these:
     - ✅ `checkout_session.payment.paid`
     - ✅ `payment.paid`
     - ✅ `checkout_session.payment.failed`
6. **Save** the webhook
7. **Test** it (PayMongo has a "Test" button)

---

### 3️⃣ VERIFY IT WORKS

**Make a test booking:**
1. Open your website as a guest
2. Book a room
3. Proceed to payment
4. Use test card: `4343434343434343`
5. Complete payment

**Check results:**
- ✅ Frontdesk panel shows "paid" status
- ✅ Email arrives in inbox (check spam too)
- ✅ Queue worker terminal shows activity

---

## 🔧 TROUBLESHOOTING

### Queue worker shows errors?
```bash
# Check failed jobs:
php artisan queue:failed

# Retry failed jobs:
php artisan queue:retry all
```

### Webhook not working?
```bash
# Run diagnostic:
diagnose-payment-system.bat

# Check webhook logs in PayMongo Dashboard
```

### Still having issues?
```bash
# Run full diagnostic:
diagnose-payment-system.bat

# Check Laravel logs:
type storage\logs\laravel.log | findstr /i "webhook"
```

---

## 📋 CHECKLIST

- [ ] Queue worker is running (terminal window open)
- [ ] Webhook registered in PayMongo Dashboard
- [ ] Webhook URL matches your ngrok URL
- [ ] Test payment completed successfully
- [ ] Booking shows "paid" in frontdesk
- [ ] Confirmation email received

---

## ⚠️ CRITICAL REMINDERS

1. **Queue worker MUST stay running** - Don't close the terminal!
2. **Ngrok URL changes** - If you restart ngrok, update webhook URL in PayMongo
3. **Check logs** - If something fails, check `storage/logs/laravel.log`

---

## 📚 MORE HELP

- **Detailed Guide:** Read `PAYMENT_FIX_INSTRUCTIONS.md`
- **Summary:** Read `PAYMENT_ISSUES_SUMMARY.md`
- **Diagnostic:** Run `diagnose-payment-system.bat`

---

## ✅ DONE!

Once both steps are complete:
- Payments will update status automatically
- Emails will send within seconds
- Frontdesk will see correct status
- Guests will receive confirmations

**You're all set! 🎉**
