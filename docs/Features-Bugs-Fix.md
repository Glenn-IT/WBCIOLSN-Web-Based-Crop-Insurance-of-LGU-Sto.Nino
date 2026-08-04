## Feature Requests

### ✅ UNLOCKED: All System Features Available (August 4, 2026)
**Status**: Under-construction gates removed - full system access enabled!

**Pages Unlocked**:

**User/Farmer Pages**:
- ✅ My Profile (`views/user/profile.php`) - View and edit profile, change password, set security question
- ✅ My Applications (`views/user/my-applications.php`) - View all insurance policy applications
- ✅ File a Claim (`views/user/file-claim.php`) - Submit claims for insured crops
- ✅ Application Status (`views/user/application-status.php`) - Track policy application progress

**Admin Pages**:
- ✅ Reports (`views/admin/reports.php`) - Analytics and reporting dashboard
- ✅ Manage Applications (`views/admin/manage-applications.php`) - Review and approve policy applications
- ✅ Claim Verification (`views/admin/claim-verification.php`) - Review and process farmer claims

All pages are now fully functional and accessible!

---

### ✅ IMPLEMENTED: OTP Email Verification for Admin-Created Accounts
**Status**: Already implemented and working!

**How it works**:
1. Admin navigates to User Management
2. When creating a new farmer account, admin enters the farmer's email
3. Admin clicks "📧 Send Code" button
4. System sends a 6-digit OTP code to the farmer's Gmail
5. Farmer receives the verification code (expires in 10 minutes)
6. Admin enters the code in the "Verification Code" field
7. System verifies the OTP before creating the account

**Files**:
- Backend: `api/controllers/UserController.php` - `sendOtp()` and `store()` methods
- Frontend: `views/admin/user-management.php` - OTP UI and validation
- Email: `api/helpers/mailer.php` - `sendOtpEmail()` function
- Model: `api/models/OtpModel.php` - OTP creation and verification

**API Endpoints**:
- `POST /api/users/send-otp` - Sends OTP code to farmer's email
- `POST /api/users` - Creates user (requires valid OTP)

---

### ✅ IMPLEMENTED: Temporary Password System
**Status**: Already implemented and working!

**How it works**:
1. When admin creates a farmer account (after OTP verification)
2. System auto-generates temporary password: `Password@123`
3. Password is emailed to the farmer
4. Account is created with `must_change_password = 1` flag
5. When farmer logs in, a **friendly notification banner** appears at the top
6. Banner reminds farmer to change password in Profile tab (not forced)
7. In Profile page, a highlighted alert shows if using temporary password
8. System clears the `must_change_password` flag after successful change

**Updated (August 4, 2026)**: Changed from forced modal to friendly notification banner. Farmers can now explore the system and change password when ready in the Profile tab.

**Files**:
- Password Generation: `api/helpers/mailer.php` - `generateTempPassword()`
- Email Template: `api/helpers/mailer.php` - `sendTempPasswordEmail()`
- Notification Banner: `includes/user-sidebar.php` - Top banner notification
- Profile Alert: `views/user/profile.php` - Highlighted change password section
- Backend: `api/controllers/UserController.php` - Sets flag during creation

---

## Bug Fixes

### ✅ IMPROVED: Password Requirements UI/UX (August 4, 2026)
**Enhancement**: Added clear, visually consistent password requirements information across all password input areas.

**Changes Made**:
- Added informative blue gradient info boxes showing password requirements
- Displays example password: `Password@123`
- Consistent design across Profile, Signup, and Forgot Password pages
- Requirements shown:
  - At least 8 characters
  - At least 1 uppercase letter (A-Z)
  - At least 1 lowercase letter (a-z)
  - At least 1 number (0-9)
  - At least 1 special character (@, #, !, etc.)

**Files Updated**:
- `views/user/profile.php` - Change Password section
- `views/user/signup.php` - Registration form
- `views/user/forgot-password.php` - Password reset form

**Design**: Blue gradient box with information icon, matches system's color scheme and design language.

---

### ✅ FIXED: Password Change 422 Validation Error (August 4, 2026)
**Issue**: When farmers tried to change their temporary password after admin-created accounts, they received a 422 Unprocessable Entity error with "Validation Failed" message.

**Root Cause**: The `sanitizeAll()` function was converting special characters in passwords (like `@`, `!`, `#`) to HTML entities, causing password verification to fail.

**Solution**: Modified the following methods in `AuthController.php` to handle passwords correctly:
- `register()` - No longer sanitizes password and security_answer fields
- `changePassword()` - Extracts password fields raw without sanitization
- `resetPassword()` - Ensures password and answer are not sanitized
- `setSecurityQuestion()` - Keeps password field raw while sanitizing other fields

**Files Modified**:
- `api/controllers/AuthController.php`

**Documentation**: See `docs/PASSWORD_SANITIZATION_FIX.md` for detailed information.
 