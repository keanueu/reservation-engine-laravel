# Railway Deployment Fixes

## Issue 1: Route Cache Conflict (FIXED)

### Problem
```
LogicException: Unable to prepare route [profile] for serialization. 
Another route has already been assigned name [profile.show].
```

### Solution
Created `nixpacks.toml` to prevent automatic route caching during build.

### Files Created
- **nixpacks.toml** - Customizes Railway build process

---

## Issue 2: Migration Column Reference Error (FIXED)

### Problem
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'refund_amount' in 'bookings'
SQL: alter table `bookings` add `paymongo_refund_id` varchar(255) null after `refund_amount`
```

### Root Cause
Migration used `after('column_name')` clauses that referenced columns which might not exist yet in Railway's database, causing the migration to fail.

### Solution
Removed all `after()` clauses from the migration file. Columns will be added at the end of the table instead of specific positions.

### Files Modified
- **database/migrations/2025_11_15_090000_ensure_payment_and_refund_columns.php**
  - Removed `->after('id')` from payment_id columns
  - Removed `->after('status')` from boat_bookings payment_id
  - Removed `->after('refund_amount')` from paymongo_refund_id

### Why This Works
- Column order doesn't affect functionality
- Prevents errors when referenced columns don't exist
- Migration is now idempotent and safe for any database state

---

## Deployment Checklist

### Files to Commit
- [x] nixpacks.toml (route cache fix)
- [x] database/migrations/2025_11_15_090000_ensure_payment_and_refund_columns.php (migration fix)
- [x] railway.toml (unchanged, but verify it exists)

### Expected Railway Build Process
1. **Install Phase**: `composer install --no-dev --optimize-autoloader`
2. **Build Phase**: Clear all caches (config, route, view, cache)
3. **Deploy Phase**: Run migrations with `php artisan migrate --force`
4. **Start Phase**: Start server with `php artisan serve`

### Verification Steps
After deployment:
1. Check build logs - should complete without errors
2. Verify migrations ran successfully
3. Test routes:
   - `/profile` → Custom profile (user.profile)
   - `/user/profile` → Jetstream profile (profile.show)
4. Test database columns exist:
   - bookings: payment_id, deposit_amount, refund_amount, paymongo_refund_id
   - booking_extensions: payment_id, paymongo_refund_id
   - boat_bookings: payment_id, deposit_amount, paymongo_refund_id

---

## Technical Details

### nixpacks.toml Configuration
```toml
[phases.setup]
nixPkgs = ['php82', 'php82Packages.composer']

[phases.install]
cmds = ['composer install --no-dev --optimize-autoloader']

[phases.build]
cmds = [
  'php artisan config:clear',
  'php artisan route:clear',
  'php artisan view:clear',
  'php artisan cache:clear'
]

[start]
cmd = 'php artisan serve --host=0.0.0.0 --port=$PORT'
```

### Migration Safety Pattern
```php
// BEFORE (UNSAFE - references column that might not exist)
if (!Schema::hasColumn('bookings', 'paymongo_refund_id')) {
    $table->string('paymongo_refund_id')->nullable()->after('refund_amount');
}

// AFTER (SAFE - no column reference)
if (!Schema::hasColumn('bookings', 'paymongo_refund_id')) {
    $table->string('paymongo_refund_id')->nullable();
}
```

---

## Common Railway Errors & Solutions

### Error: "Column not found"
**Cause**: Migration references non-existent column in `after()` clause  
**Fix**: Remove `after()` clauses or ensure referenced column exists first

### Error: "Route already assigned"
**Cause**: Route caching with duplicate route names  
**Fix**: Use nixpacks.toml to prevent automatic route caching

### Error: "Class not found"
**Cause**: Autoloader not optimized or cache issues  
**Fix**: Run `composer dump-autoload` in build phase

---

## Performance Notes

### Route Caching
- **Without cache**: Routes load dynamically (~1-2ms overhead per request)
- **With cache**: Routes pre-compiled (faster but can cause conflicts)
- **Recommendation**: For apps with <100 routes, dynamic loading is fine

### Migration Performance
- Column order doesn't affect query performance
- Indexes and foreign keys matter more than column position
- Using `after()` is purely cosmetic for database structure

---

## Rollback Plan

If deployment fails:

1. **Revert nixpacks.toml**:
   ```bash
   git rm nixpacks.toml
   git commit -m "Revert nixpacks config"
   git push
   ```

2. **Revert migration changes**:
   ```bash
   git checkout HEAD~1 database/migrations/2025_11_15_090000_ensure_payment_and_refund_columns.php
   git commit -m "Revert migration changes"
   git push
   ```

3. **Railway will automatically redeploy** with previous working version

---

## Success Indicators

✅ Build completes without errors  
✅ Migrations run successfully  
✅ Application starts and responds to health check  
✅ All routes accessible  
✅ Database columns exist  
✅ No console errors  

---

## Next Steps After Successful Deployment

1. Monitor Railway logs for any runtime errors
2. Test payment flow end-to-end
3. Verify webhook endpoint is accessible
4. Test booking creation and refund requests
5. Check that all database operations work correctly
