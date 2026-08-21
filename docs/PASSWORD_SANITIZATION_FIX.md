# Password Sanitization Bug Fix

## Issue Description

When farmers tried to change their temporary password after admin-created accounts, they received a **422 Unprocessable Entity** validation error with message "Validation Failed".

## Root Cause

The `sanitizeAll()` helper function was being used on request data that included passwords. This function uses `htmlspecialchars()` which converts special characters (like `@`, `!`, `#`) into HTML entities.

For example, the temporary password `Password@123` was being converted to something like `Password&commat;123`, causing `password_verify()` to fail since the sanitized password didn't match the hashed password in the database.

## Files Fixed

### 1. `api/controllers/AuthController.php`

#### `register()` method

- **Before**: Used `sanitizeAll($raw)` on all fields including password and security_answer
- **After**: Manually sanitize only safe fields, keep password and security_answer raw
- **Impact**: Allows users to register with passwords containing special characters

#### `changePassword()` method

- **Before**: Used `sanitizeAll($this->body())` on all fields
- **After**: Extract password fields raw, no sanitization
- **Impact**: Fixes the 422 error when changing passwords

#### `resetPassword()` method

- **Before**: Used `$this->body()` directly (was already working, but inconsistent)
- **After**: Explicitly extract fields with password and answer kept raw
- **Impact**: Maintains consistency, ensures password reset works correctly

#### `setSecurityQuestion()` method

- **Before**: Used `$this->body()` directly without sanitization
- **After**: Explicitly sanitize question but keep password and answer raw
- **Impact**: More secure and consistent approach

## Testing Steps

1. **Admin creates a farmer account**
   - Temporary password `Password@123` is generated and emailed
   - `must_change_password` flag is set to 1

2. **Farmer logs in with temporary password**
   - System shows forced password change modal
   - Farmer enters:
     - Current Password: `Password@123`
     - New Password: `NewP@ss123!`
     - Confirm Password: `NewP@ss123!`

3. **Password change should now succeed**
   - API endpoint: `POST /api/auth/change-password`
   - Returns: `200 OK` with success message
   - `must_change_password` flag is cleared
   - User can continue using the system

## Security Notes

### What Should NEVER Be Sanitized

- Passwords (current, new, temporary)
- Security question answers
- Any data that needs exact verification via `password_verify()`

### What Should Be Sanitized

- Display names (first_name, last_name)
- Email addresses (trimmed, not HTML-escaped)
- Phone numbers
- Addresses
- Security questions (pre-defined options)
- Any user-generated content displayed in HTML

## Related Files

- `api/helpers/security.php` - Contains `sanitizeAll()` and `validatePasswordStrength()`
- `includes/user-sidebar.php` - Contains the forced password change modal
- `views/user/profile.php` - Contains the password change form

## Lesson Learned

Never use blanket sanitization functions on authentication-related data. Always sanitize fields individually based on their purpose and validation requirements.
