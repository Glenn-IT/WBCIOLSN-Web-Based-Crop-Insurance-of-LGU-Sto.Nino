# Admin User Creation Guide
## How to Create Farmer Accounts with OTP Verification

This guide explains how administrators can securely create farmer accounts using the OTP (One-Time Password) email verification system.

---

## Overview

When admins create farmer accounts, the system implements a **two-step verification process**:

1. **Email Verification (OTP)** - Ensures the email belongs to the farmer
2. **Temporary Password** - Farmer receives a secure temporary password via email

---

## Step-by-Step Process

### Step 1: Navigate to User Management
1. Log in as an admin
2. Go to **User Management** from the sidebar
3. Click the **"+ Create New User"** button

### Step 2: Fill in Farmer Details
Enter the farmer's information:
- **First Name** (Required)
- **Last Name** (Required)
- **Email Address** (Required - Gmail or any email)
- **Phone Number** (Optional - PH format: 09XXXXXXXXX)

### Step 3: Send OTP Verification Code
1. After entering the email address, click **"📧 Send Code"** button
2. System will:
   - Validate the email format
   - Check if email is already registered
   - Send a **6-digit verification code** to the farmer's email
   - Show success toast: "A verification code was emailed to {email}"
3. The "Send Code" button will show a 60-second cooldown timer
4. Farmer should receive an email like this:

```
Subject: Your Verification Code – Crop Insurance System

Hi Juan,

An administrator is creating a Crop Insurance System account using 
this email address. Please provide the code below to confirm you own 
this inbox:

┌─────────────┐
│   123456    │  (6-digit code)
└─────────────┘

This code will expire in 10 minutes.

If you did not expect this, you can safely ignore this email.
```

### Step 4: Enter Verification Code
1. Ask the farmer for the 6-digit code they received
2. Enter the code in the **"Verification Code"** field
3. The code must be entered within **10 minutes** (expires after that)

### Step 5: Complete the Form
1. Select the **Role**: Farmer or Agent
2. Select the **Status**: Active, Inactive, or Suspended
3. Select the **Farmer Type**: 
   - Owner-Cultivator
   - Tenant
   - Leaseholder
   - Farm Worker
   - Other

### Step 6: Create the Account
1. Click **"💾 Save New User"** button
2. System will:
   - Verify the OTP code is correct and not expired
   - Generate temporary password: `Password@123`
   - Create the farmer account with `must_change_password = 1` flag
   - Email the temporary password to the farmer
   - Show success message with the temporary password

### Step 7: Inform the Farmer
Tell the farmer:
1. Their account has been created
2. Check their email for the temporary password
3. Log in using:
   - Email: {their email}
   - Password: `Password@123`
4. They **must change their password** on first login

---

## What the Farmer Experiences

### First Login
1. Farmer goes to the login page
2. Enters email and temporary password (`Password@123`)
3. System logs them in successfully

### Forced Password Change
1. A modal automatically appears with title **"🔒 Change Your Password"**
2. Modal says: "For your security, you must set a new password before continuing"
3. Farmer must enter:
   - **Temporary/Current Password**: `Password@123`
   - **New Password**: Min 8 chars, must include:
     - At least 1 uppercase letter (A-Z)
     - At least 1 lowercase letter (a-z)
     - At least 1 number (0-9)
     - At least 1 special character (@, #, !, etc.)
   - **Confirm New Password**: Must match
4. Click **"Set New Password"** button
5. System updates their password and clears the flag
6. Farmer can now use the system normally

---

## Email Templates

### OTP Verification Email
- **Subject**: Your Verification Code – Crop Insurance System
- **Content**: 6-digit code with 10-minute expiration
- **Purpose**: Verify farmer owns the email address

### Temporary Password Email
- **Subject**: Your New Account – Crop Insurance System  
- **Content**: Welcome message with temporary password
- **Purpose**: Allow farmer to log in for the first time

---

## Error Handling

### "Email address is already in use" (409)
- **Cause**: An account with that email already exists
- **Solution**: Use a different email or check existing users

### "Failed to send verification email" (502)
- **Cause**: SMTP configuration issue or network problem
- **Solution**: 
  - Check `.env` file has correct Gmail SMTP settings
  - Use `Admin → Mail Test` to verify email is working
  - Check internet connection

### "Invalid or expired verification code" (422)
- **Cause**: OTP code is wrong or expired (>10 minutes old)
- **Solution**: Click "Send Code" again to generate a new OTP

### "Phone number must be 11 digits in PH format"
- **Cause**: Invalid Philippine phone number format
- **Solution**: Use format `09XXXXXXXXX` (starts with 09, 11 digits total)

---

## Security Features

### OTP Code Security
- ✅ Codes are **6 digits** for easy entry
- ✅ Codes **expire after 10 minutes**
- ✅ Each email gets a **unique code**
- ✅ Codes are **one-time use** (consumed after verification)
- ✅ Rate limited: **5 OTP requests per minute** per IP

### Temporary Password Security
- ✅ Strong password: `Password@123` (meets all requirements)
- ✅ **Must be changed** on first login (enforced by system)
- ✅ User cannot access any features until password is changed
- ✅ New password must meet strict complexity requirements

---

## Technical Details

### API Endpoints
```
POST /api/users/send-otp
  Request: { email, first_name }
  Response: { success: true, message: "Verification code sent" }

POST /api/users
  Request: { first_name, last_name, email, otp, phone, role, status, farmer_type }
  Response: { success: true, data: { user, temp_password } }
```

### Database Tables
- **users** - Stores farmer accounts
- **otps** - Stores verification codes with expiration

### Files Involved
- `api/controllers/UserController.php` - Backend logic
- `api/models/OtpModel.php` - OTP management
- `api/helpers/mailer.php` - Email sending
- `views/admin/user-management.php` - Admin UI
- `includes/user-sidebar.php` - Forced password change modal

---

## Troubleshooting

### OTP Emails Not Being Received
1. Check spam/junk folder
2. Verify email address is typed correctly
3. Test email system: `Admin → Mail Test`
4. Check `.env` SMTP configuration:
   ```
   SMTP_HOST=smtp.gmail.com
   SMTP_PORT=587
   SMTP_USERNAME=your-email@gmail.com
   SMTP_PASSWORD=your-app-password
   SMTP_FROM_EMAIL=your-email@gmail.com
   SMTP_FROM_NAME=Crop Insurance System
   ```

### Temporary Password Email Not Received
- Same troubleshooting steps as OTP emails
- Check if user's email provider is blocking automated emails

### Farmer Cannot Change Password
- **Error**: "Current password is incorrect"
  - Solution: Ensure they're using the exact temporary password from email
- **Error**: "Password must contain..." 
  - Solution: New password must meet all complexity requirements

---

## Best Practices

### For Admins
1. ✅ Always verify the farmer's email before creating account
2. ✅ Inform the farmer you're creating their account
3. ✅ Tell them to check spam folder if email doesn't arrive
4. ✅ Keep the temporary password secure (don't share via text/chat)
5. ✅ Confirm farmer successfully changed password after first login

### For Farmers
1. ✅ Check email immediately after admin creates account
2. ✅ Change password as soon as you log in
3. ✅ Choose a strong, memorable password
4. ✅ Don't share your password with anyone
5. ✅ Set up security question for account recovery

---

## FAQ

**Q: Can I create accounts without OTP verification?**  
A: No, OTP verification is required for security. It ensures the email belongs to the farmer.

**Q: Can I resend the OTP if farmer didn't receive it?**  
A: Yes, wait for the 60-second cooldown, then click "Send Code" again.

**Q: What if the farmer loses the temporary password email?**  
A: You can see the temporary password in the success message after creation. Otherwise, use "Reset Password" feature.

**Q: Can I create multiple accounts with the same email?**  
A: No, each email can only have one account in the system.

**Q: How long does the temporary password work?**  
A: Forever, until the farmer changes it. There's no expiration on the temporary password itself.

**Q: Can farmers register their own accounts?**  
A: Yes, but those accounts need **admin approval** before they can log in. Admin-created accounts are automatically active.

---

**Need Help?** Contact the system administrator or refer to the technical documentation.
