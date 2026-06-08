# Backend & Database Implementation Roadmap

## Web-Based Crop Insurance System

---

## Phase 1: Database Design & Setup ✅ DONE

**Estimated Time: 1–2 weeks**

### 1.1 Database Schema Design

- [x] Define all entities and relationships (ERD)
- [x] Design tables:
  - `users` (farmers, agents, admins)
  - `farms` (farm details, location, size, crop type)
  - `policies` (insurance policy records)
  - `claims` (claim submissions)
  - `claim_documents` (uploaded evidence files)
  - `payments` (premium & payout transactions)
  - `crop_types` (reference table)
  - `coverage_plans` (available insurance plans)
  - `notifications` (system notifications)
  - `audit_logs` (activity tracking)

### 1.2 Database Creation

- [x] Write `schema.sql` migration file
- [x] Write `seeders.sql` for test/default data
- [ ] Set up MySQL database in XAMPP (run schema.sql)
- [ ] Test database connection

### 1.3 Database Configuration

- [x] Create `api/config/database.php` connection file
- [x] Set up environment variables (`.env.example`) for DB credentials
- [x] Created `api/test-db.php` connection test script
- [ ] Run `schema.sql` and `seeders.sql` in phpMyAdmin or MySQL CLI

---

## Phase 2: Backend Foundation ✅ DONE

**Estimated Time: 1 week**

### 2.1 Project Structure Setup

- [x] Organized folder structure:
  ```
  /api
    /config       ← app.php, database.php, env.php
    /models       ← BaseModel.php
    /controllers  ← BaseController.php
    /middleware   ← (Phase 3)
    /router       ← Router.php
    /helpers      ← response.php, validation.php, jwt.php, upload.php, cors.php
    /uploads      ← (runtime, not committed)
  ```
- [x] Set up `api/.htaccess` for URL routing (Apache)
- [x] Set up root `.htaccess` for SPA and `.env` protection
- [x] Created `Router` class with `{param}` support

### 2.2 Core Configuration

- [x] `config/database.php` — Singleton PDO connection
- [x] `config/app.php` — App settings (base URL, upload paths, timezone)
- [x] `config/env.php` — `.env` file loader
- [x] `api/bootstrap.php` — Single entry point that loads all config/helpers
- [x] `api/index.php` — Main API router entry point
- [x] `helpers/response.php` — JSON response helper (success, error, paginated)
- [x] `helpers/validation.php` — Input validation, sanitize, getJsonBody
- [x] `helpers/jwt.php` — JWT encode/decode (no external library)
- [x] `helpers/upload.php` — Secure file upload handler
- [x] `helpers/cors.php` — CORS headers
- [x] `models/BaseModel.php` — Reusable DB query methods (find, insert, update, delete, paginate)
- [x] `controllers/BaseController.php` — auth(), requireRole(), validateOrFail(), audit()

---

## Phase 3: Authentication & Authorization ✅ DONE

**Estimated Time: 1 week**

### 3.1 User Authentication

- [x] `POST /api/auth/register` — Farmer registration (returns JWT)
- [x] `POST /api/auth/login` — Login (returns JWT)
- [x] `POST /api/auth/logout` — Stateless logout with audit log
- [x] `POST /api/auth/forgot-password` — Send password reset email
- [x] `POST /api/auth/reset-password` — Reset password via token
- [x] `GET  /api/auth/me` — Get current authenticated user

### 3.2 Middleware

- [x] `middleware/AuthMiddleware.php` — `requireAuth()` verifies JWT
- [x] `middleware/RoleMiddleware.php` — `requireRole()` and `requireOwnerOrAdmin()`
- [x] `middleware/RateLimitMiddleware.php` — File-based rate limiting per IP

### 3.3 Role Management

- [x] Roles defined: `admin`, `agent`, `farmer`
- [x] Routes protected via `requireAuth()` + `requireRole()` in controllers
- [x] `models/UserModel.php` — Password hashing, token management, email check
- [x] `helpers/mailer.php` — Welcome email + password reset email templates

---

## Phase 4: Core API Modules ✅ DONE

**Estimated Time: 2–3 weeks**

### 4.1 User Management

- [x] `GET    /api/users` — List users with search & pagination (admin)
- [x] `GET    /api/users/{id}` — Get user profile
- [x] `PUT    /api/users/{id}` — Update profile (owner or admin)
- [x] `DELETE /api/users/{id}` — Soft deactivate user (admin)
- [x] `PUT    /api/users/{id}/status` — Set user status (admin)

### 4.2 Farm Management

- [x] `POST   /api/farms` — Register a farm
- [x] `GET    /api/farms` — List farms (own farms for farmer, all for admin/agent)
- [x] `GET    /api/farms/{id}` — Farm details with crop type & owner
- [x] `PUT    /api/farms/{id}` — Update farm info
- [x] `DELETE /api/farms/{id}` — Remove farm

### 4.3 Coverage Plans

- [x] `GET    /api/plans` — List active plans (public) / all plans (admin)
- [x] `GET    /api/plans/{id}` — Plan details
- [x] `POST   /api/plans` — Create plan (admin)
- [x] `PUT    /api/plans/{id}` — Update plan (admin)
- [x] `DELETE /api/plans/{id}` — Soft deactivate plan (admin)

### 4.4 Policy Management

- [x] `POST   /api/policies` — Apply for insurance (auto-calculates premium & coverage)
- [x] `GET    /api/policies` — List policies (own for farmer, all for admin/agent)
- [x] `GET    /api/policies/{id}` — Policy details with farm, plan, agent info
- [x] `PUT    /api/policies/{id}/approve`— Approve policy (agent/admin)
- [x] `PUT    /api/policies/{id}/reject` — Reject policy with reason (agent/admin)
- [x] `PUT    /api/policies/{id}/cancel` — Cancel policy (owner or admin)

### 4.5 Claims Management

- [x] `POST   /api/claims` — Submit a claim (must have active policy)
- [x] `GET    /api/claims` — List claims (own for farmer, all for admin/agent)
- [x] `GET    /api/claims/{id}` — Claim details with documents
- [x] `PUT    /api/claims/{id}/status` — Update claim status & approved amount (agent/admin)
- [x] `POST   /api/claims/{id}/documents` — Upload supporting documents (MIME-verified)

### 4.6 Payments

- [x] `POST   /api/payments/premium` — Record premium payment for a policy
- [x] `GET    /api/payments` — Payment history (own for farmer, all for admin)
- [x] `GET    /api/payments/{id}` — Payment details
- [x] `POST   /api/payments/payout` — Process claim payout & mark claim as paid (admin)

### Models Created

- [x] `models/FarmModel.php`
- [x] `models/PlanModel.php`
- [x] `models/PolicyModel.php`
- [x] `models/ClaimModel.php`
- [x] `models/PaymentModel.php`

---

## Phase 5: File Uploads & Storage ✅ DONE (implemented in Phase 2)

**Estimated Time: 3–4 days**

- [x] `helpers/upload.php` — Secure file upload handler
- [x] Validate file types (PDF, JPG, PNG) + actual MIME verification
- [x] Enforce max file size via `UPLOAD_MAX_SIZE` env variable
- [x] Store files in `/uploads/{claims|profiles|general}`
- [x] Direct access to `/uploads` blocked via `.htaccess`

---

## Phase 6: Notifications ✅ DONE

**Estimated Time: 3–4 days**

- [x] `GET    /api/notifications` — Paginated notifications for current user
- [x] `POST   /api/notifications` — Send notification to a user (admin/agent)
- [x] `GET    /api/notifications/unread-count` — Count unread notifications
- [x] `PUT    /api/notifications/read-all` — Mark all as read
- [x] `PUT    /api/notifications/{id}/read` — Mark single notification as read
- [x] `DELETE /api/notifications/clear` — Delete all read notifications
- [x] `helpers/notification.php` — Global `notify()` + preset helpers:
  - [x] `notifyPolicyApproved()` — fired on policy approval
  - [x] `notifyPolicyRejected()` — fired on policy rejection
  - [x] `notifyClaimStatusUpdated()` — fired on every claim status change
  - [x] `notifyPaymentReceived()` — fired on premium payment
  - [x] `notifyPayoutProcessed()` — fired on claim payout
- [x] Email notification wired into every notify() call via `sendMail()`

---

## Phase 7: Reports & Dashboard Data ✅ DONE

**Estimated Time: 3–4 days**

- [x] `GET /api/dashboard/stats` — Admin summary OR farmer personal stats (role-aware)
- [x] `GET /api/reports/claims` — Filterable claims report (status, date range)
- [x] `GET /api/reports/policies` — Filterable policies report (status, date range)
- [x] `GET /api/reports/payments` — Filterable payments report (type, date range)
- [x] `GET /api/reports/trends/premium` — Monthly premium trend (last 12 months)
- [x] `GET /api/reports/claims/by-crop` — Claims grouped by crop type
- [x] `GET /api/reports/claims/by-province` — Top 10 provinces by claim count
- [x] `GET /api/reports/export/{type}` — CSV export for claims/policies/payments (UTF-8 BOM for Excel)

---

## Phase 8: Security & Optimization ✅ DONE

**Estimated Time: 3–4 days**

- [x] Input sanitization on all endpoints — `sanitizeAll()` applied to every `$this->body()` call across all controllers
- [x] SQL injection prevention — `guardSqlInjection()` guards all user-supplied body data; prepared statements used throughout BaseModel
- [x] Positive integer assertion — `assertPositiveInt()` applied to all `{id}` route parameters before DB lookups
- [x] CORS headers configuration — `cors.php` with allowed origins + OPTIONS preflight handler
- [x] Password hashing (bcrypt) — `password_hash(PASSWORD_BCRYPT)` in `UserModel::createUser()`; strength enforced via `validatePasswordStrength()`
- [x] API rate limiting — `RateLimitMiddleware` with context buckets (login/register/forgot) + escalating block durations
- [x] Audit logging for sensitive actions — `BaseController::audit()` called on every mutating action
- [x] HTTPS enforcement (production) — `enforceHttps()` in bootstrap; skipped in `APP_ENV=development`
- [x] Security headers — `sendSecurityHeaders()` sends CSP, X-Frame-Options, HSTS, etc. on every request
- [x] Max payload enforcement — `enforceMaxPayload(2MB)` in bootstrap
- [x] JWT secret strength assertion — `assertSecureJwtSecret()` in bootstrap

---

## Phase 9: Testing

**Estimated Time: 1 week**

- [ ] Unit test models and helpers
- [ ] API endpoint testing (Postman collection)
- [ ] Database query optimization
- [ ] Load testing basic scenarios
- [ ] Fix bugs and edge cases

---

## Summary Timeline

| Phase | Description             | Duration |
| ----- | ----------------------- | -------- |
| 1     | Database Design & Setup | Week 1–2 |
| 2     | Backend Foundation      | Week 2–3 |
| 3     | Authentication          | Week 3   |
| 4     | Core API Modules        | Week 4–6 |
| 5     | File Uploads            | Week 6   |
| 6     | Notifications           | Week 7   |
| 7     | Reports                 | Week 7   |
| 8     | Security & Optimization | Week 8   |
| 9     | Testing                 | Week 8–9 |

---

> **Tech Stack:** PHP (vanilla), MySQL, JWT Auth, PHPMailer, XAMPP
