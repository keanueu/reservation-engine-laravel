# PayMongo Webhook Quick Reference

## 🚀 Quick Deploy
```bash
# Run deployment script
deploy-webhook-fix.bat

# Or manual:
php artisan cache:clear
php artisan config:clear
php artisan queue:restart
```

## ✅ Quick Test
```bash
# Test endpoint
curl -X POST https://yourdomain.com/webhooks/paymongo -d '{"test":"data"}'

# Expected: HTTP 200 OK
```

## 📊 Quick Check
```bash
# Check logs
tail -f storage/logs/laravel.log | grep "\[Webhook\]"

# Check queue
ps aux | grep "queue:work"

# Check SSL
curl -I https://yourdomain.com/webhooks/paymongo
```

## 🔧 Quick Fixes

### Webhook Disabled?
1. Check PayMongo dashboard
2. Click "Enable"
3. Send test event
4. Monitor logs

### Slow Response?
```sql
-- Add indexes
CREATE INDEX idx_bookings_payment_id ON bookings(payment_id);
CREATE INDEX idx_bookings_group_id ON bookings(group_id);
```

### Queue Not Working?
```bash
# Start queue worker
php artisan queue:work --daemon

# Check failed jobs
php artisan queue:failed
```

### SSL Error?
```bash
# Test SSL
openssl s_client -connect yourdomain.com:443

# Use Cloudflare SSL (easiest)
# Or Let's Encrypt
```

## 🎯 Critical Rules

1. **ALWAYS return 200 OK** ✅
2. **Respond within 3 seconds** ⏱️
3. **Handle ALL event types** 🎭
4. **Never throw exceptions** 🛡️
5. **Log everything** 📝

## 📞 Emergency Contacts

- **PayMongo Support**: support@paymongo.com
- **Status Page**: https://status.paymongo.com
- **Docs**: https://developers.paymongo.com/docs/webhooks

## 📚 Full Documentation

See `WEBHOOK_FIX_COMPLETE_GUIDE.md` for:
- Complete troubleshooting guide
- Performance optimization
- Security best practices
- Monitoring setup
- Emergency recovery

---

**Last Updated**: 2025
**Status**: ✅ Production Ready
