# Gaps Audit — Web-Based Crop Insurance System

> Generated: 2026-06-09

---

## Database Gaps

### CRITICAL — `policy_documents` Table Missing from Schema

- **Schema file**: `database/schema.sql` — no `policy_documents` table defined
- **Broken references**:
  - `api/models/PolicyModel.php:89–110` — `getDocuments()` and `addDocument()` query this table
  - `api/controllers/PolicyController.php:278–303` — `uploadDocument()` calls `addDocument()`
- **Impact**: Policy document upload will throw a SQL "table not found" error at runtime.
- **Fix**: Create a `policy_documents` table mirroring `claim_documents` with columns: `id`, `policy_id`, `document_type`, `file_name`, `file_path`, `file_type`, `file_size`, `uploaded_at`.

---

### CRITICAL — `latitude` and `longitude` Columns Missing in `farms` Table

- **Schema file**: `database/schema.sql:45–61` — no `latitude` or `longitude` columns in `farms`
- **Broken references**:
  - `api/controllers/FarmController.php:93–94` — stores lat/lng on create
  - `api/controllers/FarmController.php:135` — update method ignores lat/lng entirely
  - `api/models/PolicyModel.php:31–32` — SELECT includes latitude/longitude
  - `views/user/new-application.php` — submits lat/lng from Leaflet map
  - `views/user/my-applications.php` — displays farm location
- **Impact**: Geo-tagging fails silently or throws SQL errors. Coordinates submitted by the frontend are never persisted. Update also doesn't carry the fields even if columns were added.
- **Fix**: Add `latitude DECIMAL(10, 8)` and `longitude DECIMAL(11, 8)` to `farms` table. Also patch `FarmController.php` update method to include these fields.

---

### HIGH — Application Status Timeline References Undefined Schema Fields

- **File**: `views/user/application-status.php:137–165`
- **Missing fields** (not in schema):
  - `farm_verified_at` (line 137)
  - `damage_reviewed_at` (line 144)
  - `coverage_set_at` (line 151)
  - `rejected_at` (line 158)
  - `rejection_reason` (line 160)
  - `final_decision_at` (line 158)
- **Impact**: Timeline tracker shows `null` / empty for all verification stage dates.
- **Fix**: Either add these timestamp columns to the `policies` table via a new migration, or derive them from the existing `updated_at` + `status` fields.

---

## Backend Gaps

### CRITICAL — Email Not Configured

- **File**: `.env:32–37`
- **Placeholders present**:
  ```
  MAIL_USERNAME=your_email@gmail.com
  MAIL_PASSWORD=your_app_password
  MAIL_HOST=smtp.gmail.com
  ```
- **Affected features**: Forgot-password email, welcome email on register, claim notifications.
- **Extra issue**: `api/controllers/AuthController.php:66,139` wraps `sendWelcomeEmail()` and `sendPasswordResetEmail()` with the `@` error suppressor — failures are hidden and the user is told the email was sent even when it wasn't.
- **Fix**: Fill in real SMTP credentials. Remove `@` suppressor and return a proper error response if mail fails.

---

### HIGH — Payment Status Hardcoded as "completed"

- **File**: `api/controllers/PaymentController.php:77` (premium), `115` (payout)
- **Issue**: Every payment record is immediately inserted with `status = 'completed'` regardless of whether actual payment was received. No gateway integration (GCash, PayMaya, etc.) exists.
- **Impact**: System cannot verify real payments; anyone can trigger a "completed" payment record.
- **Fix**: Integrate a payment gateway or implement a manual verification workflow (pending → verified by admin → completed).

---

### MEDIUM — Farm Geo-Location Cannot Be Updated

- **File**: `api/controllers/FarmController.php:135`
- **Issue**: The `update()` method does not include `latitude` or `longitude` in the UPDATE query. Even after the schema is fixed, updating a farm will not save new coordinates.
- **Fix**: Add `latitude` and `longitude` to the update payload in `FarmController.php`.

---

### MEDIUM — Claim Document Upload Has Weak Error Handling

- **File**: `views/user/file-claim.php:252–269`
- **Issue**: `uploadClaimDocument()` is wrapped in a try-catch that swallows errors and only shows a warning toast. The claim submission continues even if the photo upload failed.
- **Impact**: Claims can be submitted without attached photo evidence, with no enforced retry.
- **Fix**: Block claim finalization if required document upload fails.

---

### LOW — Report CSV Export Does Not Sanitize Data

- **File**: `api/controllers/ReportController.php:159–169`
- **Issue**: `fputcsv()` is called directly on raw database values. Fields containing commas or quotes (e.g., farm names) will produce malformed CSV rows.
- **Fix**: Sanitize or wrap each field value before passing to `fputcsv()`.

---

### LOW — No DELETE Route for Claims

- **File**: `api/index.php:90–95`
- **Issue**: Claims routes only cover GET, POST, and PUT. No DELETE endpoint is registered.
- **Impact**: Claims cannot be removed via the API.

---

## Frontend Gaps

### HIGH — Profile Photo Upload is a Placeholder

- **File**: `views/user/profile.php:45`
- **Issue**: The photo upload button calls `showToast('Info', 'Photo upload is not available yet.', 'info')`. No upload form or API endpoint exists.
- **Impact**: Users cannot set a profile photo; the UI implies the feature exists.
- **Fix**: Either implement photo upload (add `POST /users/{id}/photo` endpoint + `multer`-style handling) or remove the button entirely.

---

### MEDIUM — Dashboard Stats May Show Empty/Incorrect Data

- **File**: `views/user/dashboard.php:196–225`
- **Issues**:
  - Line 220 expects `d.farms` as an object, but `ReportModel.farmerStats()` returns an integer.
  - Line 224 references `d.payments?.total_payout_received`, which is not guaranteed in the API response.
- **Impact**: Some stat cards display `—` instead of actual values.
- **Fix**: Align the API response shape from `ReportController` with what the dashboard JS expects.

---

## Configuration & Security

### SECURITY — JWT Secret Visible in `.env`

- **File**: `.env:23`
- **Issue**: The `JWT_SECRET` is committed in the repository. If the repo is ever public or the `.env` is leaked, all JWT tokens can be forged.
- **Fix**: Rotate the secret immediately if the repo has been shared. Ensure `.env` is listed in `.gitignore` and never committed.

---

### LOW — Upload Directory Path is Relative

- **File**: `.env:28` — `UPLOAD_PATH=uploads/`
- **Issue**: Relative path depends on the working directory of the PHP process. On some XAMPP/Apache setups this resolves incorrectly, causing uploads to silently fail.
- **Fix**: Use an absolute path (e.g., `C:/xampp/htdocs/web-based-crop-insurance/uploads/`).

---

## Summary Table

| #   | Severity | Area     | Issue                                                | File(s)                                                 |
| --- | -------- | -------- | ---------------------------------------------------- | ------------------------------------------------------- |
| 1   | CRITICAL | Database | `policy_documents` table missing                     | `schema.sql`, `PolicyModel.php`, `PolicyController.php` |
| 2   | CRITICAL | Database | `latitude`/`longitude` columns missing in `farms`    | `schema.sql`, `FarmController.php`, `PolicyModel.php`   |
| 3   | CRITICAL | Backend  | Email SMTP not configured; failures suppressed       | `.env`, `AuthController.php`                            |
| 4   | HIGH     | Database | Timeline fields missing from `policies` schema       | `schema.sql`, `application-status.php`                  |
| 5   | HIGH     | Backend  | Payment always marked "completed" — no gateway       | `PaymentController.php`                                 |
| 6   | HIGH     | Frontend | Profile photo upload is a non-functional placeholder | `profile.php`                                           |
| 7   | MEDIUM   | Backend  | Farm update ignores lat/lng even when columns exist  | `FarmController.php`                                    |
| 8   | MEDIUM   | Backend  | Claim photo upload errors are silently swallowed     | `file-claim.php`                                        |
| 9   | MEDIUM   | Frontend | Dashboard stat cards expect wrong API response shape | `dashboard.php`, `ReportController.php`                 |
| 10  | LOW      | Backend  | CSV export does not escape field values              | `ReportController.php`                                  |
| 11  | LOW      | Backend  | No DELETE route for claims                           | `api/index.php`                                         |
| 12  | SECURITY | Config   | JWT secret committed in `.env`                       | `.env`                                                  |
| 13  | LOW      | Config   | Upload path is relative, not absolute                | `.env`                                                  |
