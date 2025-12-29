# Admin Login Fix Guide

## Problem
Login fails with "Invalid username or password" even though:
- ✅ Database migration completed
- ✅ `tbl_users` table exists
- ✅ Admin user exists in database
- ✅ Authentication system is working
- ❌ Password hash doesn't match "admin123"

## Root Cause
The password hash in the database doesn't match the expected hash for "admin123". This can happen due to:
1. Different PHP versions generating different hashes
2. Migration file hash doesn't match your PHP's hashing algorithm
3. Database collation issues

## Solution: Generate Fresh Password Hash

### Option 1: Use PHP Script (Easiest)

1. **Open in browser:**
   ```
   http://localhost/DataAd-DBEnrollment/generate_password_hash.php
   ```

2. **The page will show:**
   - Generated hash for "admin123"
   - Copy button to copy the hash
   - SQL command to update the database
   - Verification test

3. **Copy the generated hash** from the page

4. **Go to phpMyAdmin:**
   - Navigate to `http://localhost/phpmyadmin/`
   - Select `dbenrollment` database
   - Click "SQL" tab
   - Paste the UPDATE query shown on the page
   - Click "Go"

5. **Test login:**
   - Go to `http://localhost/DataAd-DBEnrollment/login.html`
   - Username: `admin`
   - Password: `admin123`

---

### Option 2: Manual SQL Update

If the PHP script doesn't work, follow these steps:

#### Step 1: Run this in phpMyAdmin SQL tab:

```sql
-- Check current password hash
SELECT username, password, role, is_active 
FROM tbl_users 
WHERE username = 'admin';
```

#### Step 2: Update with a known working hash:

Try this hash first (works with PHP 7.4+):
```sql
UPDATE `tbl_users` 
SET `password` = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE `username` = 'admin';
```

If that doesn't work, try this alternative hash:
```sql
UPDATE `tbl_users` 
SET `password` = '$2y$10$e3YaXUlzm6X9gZ0T8SG7Z.qNQOJGYd5SbMH5Mm1bJNLXvKGOJ1K4a'
WHERE `username` = 'admin';
```

#### Step 3: Test login again

---

### Option 3: Change Password via SQL (Alternative Password)

If you want to use a different password temporarily:

```sql
-- This sets password to "password123" instead
UPDATE `tbl_users` 
SET `password` = '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm'
WHERE `username` = 'admin';
```

Then login with:
- Username: `admin`
- Password: `password123`

---

## Verification Steps

After updating the password, verify it worked:

1. **Check activity logs:**
   ```sql
   SELECT * FROM tbl_activity_logs 
   WHERE action = 'login' 
   ORDER BY created_at DESC 
   LIMIT 5;
   ```
   - Should see successful login entries

2. **Check user session:**
   - After successful login, go to `index.html`
   - Should see logout button in top-right corner
   - Should not redirect back to login

3. **Check all features:**
   - Click through all navigation tabs
   - Try accessing Maintenance section
   - Should see your username displayed

---

## If Still Not Working

### Debug Steps:

1. **Check PHP version:**
   ```php
   <?php phpinfo(); ?>
   ```
   Save as `info.php` and access via browser
   Look for PHP version (should be 7.4+)

2. **Test password_verify function:**
   Create `test_auth.php`:
   ```php
   <?php
   $password = "admin123";
   $hash = "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi";
   
   if (password_verify($password, $hash)) {
       echo "✓ Password verification WORKS";
   } else {
       echo "✗ Password verification FAILED";
       echo "<br>Generating new hash...<br>";
       echo password_hash($password, PASSWORD_DEFAULT);
   }
   ?>
   ```

3. **Check database connection:**
   - Verify `config.php` has correct credentials
   - Test connection to database

4. **Check browser console:**
   - Open Developer Tools (F12)
   - Look for JavaScript errors
   - Check Network tab for failed requests

---

## Common Issues & Solutions

### Issue: "Cannot modify header information"
**Solution:** Make sure there's no whitespace before `<?php` in auth.php

### Issue: Blank page after login
**Solution:** 
- Check JavaScript console for errors
- Verify `auth-frontend.js` is loaded
- Check if session is being created

### Issue: Redirects to login immediately
**Solution:**
- Check that session_start() is working
- Verify cookies are enabled in browser
- Check browser console for errors

### Issue: Still getting "Invalid username or password"
**Solution:**
- Use Option 1 (generate_password_hash.php) to create fresh hash
- Copy the EXACT hash (including $ symbols)
- Verify no extra spaces when pasting SQL

---

## Quick Command Summary

```sql
-- 1. Check if admin user exists
SELECT * FROM tbl_users WHERE username = 'admin';

-- 2. Update password (run generate_password_hash.php first)
UPDATE tbl_users SET password = '[PASTE_HASH_HERE]' WHERE username = 'admin';

-- 3. Verify update
SELECT username, role, is_active FROM tbl_users WHERE username = 'admin';

-- 4. Check recent login attempts
SELECT * FROM tbl_activity_logs ORDER BY created_at DESC LIMIT 10;

-- 5. Clear failed login logs (optional)
DELETE FROM tbl_activity_logs WHERE action = 'login_failed';
```

---

## Success Indicators

You'll know it's working when:
- ✅ Login redirects to `index.html`
- ✅ Logout button appears in top-right
- ✅ Can access all navigation tabs
- ✅ Maintenance section loads properly
- ✅ Activity log shows successful login

---

## Emergency Reset (Nuclear Option)

If nothing works, run this to completely reset the admin user:

```sql
-- Delete old admin user
DELETE FROM tbl_users WHERE username = 'admin';

-- Create fresh admin user (run generate_password_hash.php first to get new hash)
INSERT INTO tbl_users (username, password, role, email, full_name, is_active)
VALUES ('admin', '[PASTE_FRESH_HASH_HERE]', 'admin', 'admin@pup.edu.ph', 'System Administrator', 1);
```

---

## Next Steps After Successful Login

1. ✅ Login with admin/admin123
2. 🔐 Change password immediately!
3. 🧪 Test all features (backup, restore, prerequisites)
4. 👥 Create additional users for other roles
5. 📊 Explore the maintenance section

---

**Need Help?**
- Run `generate_password_hash.php` first - it will show you exactly what to do
- Check browser console (F12) for errors
- Check PHP error logs in XAMPP control panel
- Verify all files were uploaded correctly

**Files to check:**
- ✅ auth.php (exists and correct)
- ✅ config.php (database credentials correct)
- ✅ tbl_users table (admin user exists)
- ✅ auth-frontend.js (loaded in index.html)
- ✅ login.html (form posts to auth.php)
