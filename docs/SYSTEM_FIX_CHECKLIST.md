# System Fix & HTML-to-PHP Conversion Checklist

> **Goal:** Convert all HTML views to PHP, wire the frontend to the live API, and make the system fully functional end-to-end.
> **Current State:** Backend API (PHP) is 100% built. All 20 views are still pure HTML using `localStorage` — zero real API integration.

---

## PHASE 0 — Database Setup (Do This First)

- [x] Open **phpMyAdmin** and run `database/schema.sql` to create the `crop_insurance_db` database and all 10 tables
- [x] Run `database/seeders.sql` to insert default admin, test farmer, and coverage plans
- [x] Run `database/migration_add_missing_fields.sql`
- [x] Run `database/migration_add_verification_fields.sql`
- [x] Copy `.env.example` → `.env` and fill in your credentials:
  - `DB_NAME=crop_insurance_db`
  - `DB_USER=root`
  - `DB_PASS=` _(leave blank for XAMPP default)_
  - `JWT_SECRET=` _(set a long random string)_
  - `APP_URL=http://localhost/web-based-crop-insurance`
- [x] Visit `http://localhost/web-based-crop-insurance/api/health` — should return `{"status":"ok"}`
- [x] Visit `http://localhost/web-based-crop-insurance/api/test-db.php` — should confirm DB connection

---

## PHASE 1 — Project Structure: Convert HTML → PHP

The entire `views/` folder and `index.html` must become `.php` files. PHP files allow includes, auth guards, and server-side rendering.

### Entry Point

- [x] Rename `index.html` → `index.php`
- [x] Update `.htaccess` root rewrite rule from `index.html` to `index.php`

### Create Shared PHP Includes (new folder: `includes/`)

- [x] Create `includes/head.php` — shared `<head>` tag (charset, viewport, CSS link, favicon)
- [x] Create `includes/user-sidebar.php` — user sidebar nav (replaces the copy-pasted sidebar in every user view)
- [x] Create `includes/admin-sidebar.php` — admin sidebar nav (replaces the copy-pasted sidebar in every admin view)
- [x] Create `includes/topbar.php` — shared topbar with breadcrumb slot and notification bell
- [x] Create `includes/toast.php` — shared `<div id="toast-container">` and footer scripts
- [x] Create `includes/auth-guard.php` — PHP session/JWT check; redirects to login if not authenticated

### User Views: Rename & Convert

| Old File (HTML)                      | New File (PHP)                      | Status                                 |
| ------------------------------------ | ----------------------------------- | -------------------------------------- |
| `index.html`                         | `index.php`                         | - [x]                                  |
| `views/user/signup.html`             | `views/user/signup.php`             | - [x]                                  |
| `views/user/login.html`              | `views/user/login.php`              | - [x] (user login is root `index.php`) |
| `views/user/forgot-password.html`    | `views/user/forgot-password.php`    | - [x]                                  |
| `views/user/dashboard.html`          | `views/user/dashboard.php`          | - [x]                                  |
| `views/user/new-application.html`    | `views/user/new-application.php`    | - [x]                                  |
| `views/user/my-applications.html`    | `views/user/my-applications.php`    | - [x]                                  |
| `views/user/application-status.html` | `views/user/application-status.php` | - [x]                                  |
| `views/user/file-claim.html`         | `views/user/file-claim.php`         | - [x]                                  |
| `views/user/profile.html`            | `views/user/profile.php`            | - [x]                                  |

### Admin Views: Rename & Convert

| Old File (HTML)                        | New File (PHP)                        | Status |
| -------------------------------------- | ------------------------------------- | ------ |
| `views/admin/login.html`               | `views/admin/login.php`               | - [x]  |
| `views/admin/dashboard.html`           | `views/admin/dashboard.php`           | - [x]  |
| `views/admin/view-applications.html`   | `views/admin/view-applications.php`   | - [x]  |
| `views/admin/manage-applications.html` | `views/admin/manage-applications.php` | - [x]  |
| `views/admin/claim-verification.html`  | `views/admin/claim-verification.php`  | - [x]  |
| `views/admin/reports.html`             | `views/admin/reports.php`             | - [x]  |
| `views/admin/admin-profile.html`       | `views/admin/admin-profile.php`       | - [x]  |
| `views/admin/user-management.html`     | `views/admin/user-management.php`     | - [x]  |

### Each PHP view must:

- [ ] Replace the inline `<!DOCTYPE html>` head block with `<?php include '../../includes/head.php'; ?>`
- [ ] Replace the copy-pasted sidebar HTML with `<?php include '../../includes/user-sidebar.php'; ?>` (or admin)
- [ ] Replace the copy-pasted topbar HTML with `<?php include '../../includes/topbar.php'; ?>`
- [ ] Add `<?php require_once '../../includes/auth-guard.php'; ?>` at the top of all protected pages
- [ ] Replace the `<script src="../../data/mockData.js">` reference with the real `app.js` only
- [ ] Delete all `localStorage` reads/writes from inline `<script>` blocks

---

## PHASE 2 — Authentication Integration

### Login Pages (`index.php` and `views/user/login.php`)

- [x] `handleLogin(event)` wired to `api('POST', '/auth/login', { email, password })`
- [x] On success: `setSession(user, token)` called, redirects to `views/user/dashboard.php`
- [x] On failure: toast shown with API error message

### Admin Login (`views/admin/login.php`)

- [x] `handleAdminLogin(event)` wired to `api('POST', '/auth/login', { email, password })`
- [x] Role check: rejects non-admin/agent users
- [x] On success: `setSession(user, token)`, navigates to `views/admin/dashboard.php`

### Signup (`views/user/signup.php`)

- [x] `handleSignup(event)` calls `api('POST', '/auth/register', { first_name, last_name, email, password, phone, address })`
- [x] On success: `setSession()` called, redirects to `dashboard.php`

### Forgot Password (`views/user/forgot-password.php`)

- [x] Step 1 email form calls `api('POST', '/auth/forgot-password', { email })`
- [x] Step 3 reset form reads `?token=` from URL via `URLSearchParams`, calls `api('POST', '/auth/reset-password', { token, password })`
- [x] Password strength indicator on new password field
- [x] Step 4 success state redirects to login

### Auth Guards (All protected pages)

- [x] All protected pages use server-side `auth-guard.php` (PHP JWT verification) — JS `requireAuth()`/`requireAdmin()` not needed on converted pages

---

## PHASE 3 — User Dashboard Integration (`views/user/dashboard.php`)

- [x] Hardcoded community stats replaced with real farmer insurance summary from `/dashboard/stats`
- [x] `api('GET', '/dashboard/stats')` → populates `#stat-apps`, `#stat-approved`, `#stat-pending`, `#stat-claims`
- [x] Insurance summary card: registered farms, active coverage, payout received, claims approved
- [x] `api('GET', '/policies')` → renders Recent Applications table (latest 5)
- [x] `api('GET', '/claims')` → powers damage causes bar chart using `incident_type`
- [x] `statusChart` (doughnut) wired with live policy status counts
- [x] `damageChart` (bar) built from actual claims data
- [x] No `mockData.js` / localStorage usage

---

## PHASE 4 — New Application Form (`views/user/new-application.php`)

- [x] Step 1: Farmer name pre-filled server-side from `$authUser`
- [x] Step 2: Collects `farmLocation`, `totalArea`, `landCategory`, `tenuralStatus`, `plantingDate`, `harvestDate`, `plantingMethod`
- [x] Step 3: Collects `causeOfDamage`, `percentDamage`, `financialDamage`, `damageDescription`, `dateOfLoss`
- [x] Step 4: Dynamic plan selector — `loadPlans()` fires on entering Step 4, populates `<select id="planId">` from `api('GET', '/plans')`; plan info card shows coverage %, type, max amount; auto-fills coverage amount from selected plan
- [x] Final submit: `api('POST', '/farms', farmData)` → uses selected `plan.id` in `api('POST', '/policies', ...)`
- [x] On success: redirects to `my-applications.php` with toast
- [x] No `localStorage` / `saveApplications()` usage; removed stale `localStorage.getItem('lgu_token')` session check

---

## PHASE 5 — My Applications (`views/user/my-applications.php`)

- [x] Replace `getApplications()` localStorage call with `api('GET', '/policies')`
- [x] Render the application list from API response
- [x] The **View Details** modal calls `api('GET', '/policies/{id}')` for fresh data; "File Claim" button passes `?policy_id=` to file-claim.php
- [x] The **Edit** modal (Pending only) calls `api('GET', '/policies/{id}')` + `api('GET', '/farms/{id}')` to populate; saves via `api('PUT', '/farms/{id}')` + `api('PUT', '/policies/{id}')`
- [x] The **Cancel** button calls `api('PUT', '/policies/{id}/cancel')`
- [x] Status filter option values corrected (`active` for Approved, `pending`/`rejected`/`cancelled`)
- [x] All `showToast` calls fixed to 3-arg signature `(title, message, type)`
- [x] Client-side search/filter on `allApps` (covers all loaded records without extra API round-trips)

---

## PHASE 6 — Application Status (`views/user/application-status.php`)

- [x] Read the policy ID from the URL query string (`?id=...`) via `URLSearchParams`
- [x] With `?id=`: calls `api('GET', '/policies/{id}')`, renders single-policy detail view with 5-stage timeline
- [x] 5-stage timeline uses: `created_at`, `farm_verified_at`, `damage_reviewed_at`, `coverage_set_at`, `approved_at`/`rejected_at`/`final_decision_at`
- [x] Each stage shows icon, label, date (if available), description; connector line fills as stages complete
- [x] Rejection reason / remarks shown in amber/red alert box below policy header
- [x] "← Back to All Applications" button returns to list view
- [x] Without `?id=`: shows all-policies card grid (existing behavior, preserved)
- [x] Each card now has a "View Full Timeline →" button linking to `?id=X`
- [x] Status filter tabs updated to use API values (`active` instead of `approved`)

---

## PHASE 7 — File a Claim (`views/user/file-claim.php`)

- [x] On page load, calls `api('GET', '/policies?status=active')` and filters for `active`/`approved` to populate dropdown
- [x] `?policy_id=` URL param auto-selects the policy and pre-fills form fields (linked from my-applications view modal)
- [x] Form collects: `policy_id`, `incident_type`, `damage_percentage`, `incident_date`, `description`; `estimated_loss` calculated client-side from coverage × damage%
- [x] Submit calls `api('POST', '/claims', {...})` with `showLoading()`/`hideLoading()` wrapper
- [x] After successful submit, `uploadClaimDocument(claimId)` uploads the photo via `fetch` + `FormData` to `/claims/{id}/documents` with Bearer token
- [x] File validation: only JPG, PNG, PDF allowed; max 10 MB; PDF shows filename instead of preview image
- [x] `api('GET', '/claims')` renders existing claims list with claim number, incident type, date, amount, status badge
- [x] All `showToast` calls fixed to 3-arg `(title, message, type)`
- [x] No localStorage usage

---

## PHASE 8 — User Profile (`views/user/profile.php`)

- [x] On load, calls `api('GET', '/auth/me')` and populates all fields; falls back to PHP `$authUser` if API fails
- [x] Profile update sends `first_name`, `last_name`, `phone`, `address`, `farmer_type` via `api('PUT', '/users/{id}')`
- [x] After save, re-fetches `/auth/me` to repopulate with authoritative server data; calls `initTopbarUser()`
- [x] Password change calls `api('POST', '/auth/change-password', { current_password, new_password })`
- [x] `localStorage.setItem('lgu_current_user', ...)` removed from `saveProfile()`
- [x] All `showToast` calls fixed to 3-arg; `showLoading()`/`hideLoading()` added to both save operations

---

## PHASE 9 — Admin Dashboard (`views/admin/dashboard.php`)

- [x] Call `api('GET', '/dashboard/stats')` and populate all 5 stat cards
- [x] Wire all 4 Chart.js charts with live API data:
  - Status distribution doughnut → `/dashboard/stats` (pre-aggregated, includes cancelled)
  - Monthly apps vs claims bar → `/reports/policies` + `/reports/claims` (grouped by created_at month, last 6)
  - Claims by crop type bar → `api('GET', '/reports/claims/by-crop')` (returns crop_type + total_claims)
  - Monthly premium trend line → `api('GET', '/reports/trends/premium')` (returns month + total)
- [x] Recent Applications table → `api('GET', '/policies?limit=5')`
- [x] Recent Claims table → `api('GET', '/claims?limit=5')`
- [x] All `showToast` calls fixed to 3-arg `(title, message, type)`

---

## PHASE 10 — Manage Applications (`views/admin/manage-applications.php`)

- [x] Load all policies via `api('GET', '/policies')` with client-side search/filter
- [x] **Approve** button → `api('PUT', '/policies/{id}/approve', { remarks })`
- [x] **Reject** button → `api('PUT', '/policies/{id}/reject', { remarks })` — remarks required; validated client-side before submit
- [x] **Mark Under Review** → `api('PUT', '/policies/{id}/review', { remarks })`
- [x] **Set to Pending** → `api('PUT', '/policies/{id}', { status: 'pending', remarks })`
- [x] **Save Changes** (edit form) → `api('PUT', '/policies/{id}', { farm_location, coverage_amount, percent_damage, remarks, farm_verification, damage_verification, coverage_verification })`
- [x] **Delete** → `api('DELETE', '/policies/{id}')` with confirmation modal
- [x] After any status change or edit, `loadApplications()` re-fetches the full table
- [x] `showLoading()`/`hideLoading()` added to all async operations
- [x] All `showToast` calls fixed to 3-arg `(title, message, type)`

---

## PHASE 11 — View Applications (`views/admin/view-applications.php`)

- [x] Load all policies via `api('GET', '/policies?per_page=200')` with client-side search/filter
- [x] Stat cards derive counts from loaded array (total, approved/active, pending+under_review, rejected)
- [x] Status filter updated: added `under_review` and `rejected` options
- [x] `premium_amount` → `total_premium` fixed in both table and detail modal (matched to API field)
- [x] `app.location` → `app.farm_location` fixed in detail modal
- [x] Detail modal expanded: email, coverage_type, percent_damage, cause_of_damage, all 3 verification badges, agent name, remarks alert
- [x] `showLoading()`/`hideLoading()` added to `loadApps()` and `viewApp()`
- [x] try/catch added to both async functions

---

## PHASE 12 — Claim Verification (`views/admin/claim-verification.php`)

- [x] Load all claims via `api('GET', '/claims?per_page=200')` with tab filter (pending/approved/rejected)
- [x] **Approve claim** → `api('PUT', '/claims/{id}/status', { status: 'approved', approved_amount, remarks })` — validates amount > 0 before submit
- [x] **Reject claim** → `api('PUT', '/claims/{id}/status', { status: 'rejected', remarks })` — validates remarks non-empty before submit
- [x] **Quick approve/reject** buttons in table rows use same endpoint; quick approve pre-fills `estimated_loss` as amount
- [x] View claim documents — renders `documents` array from `api('GET', '/claims/{id}')` as clickable links
- [x] `admin_remarks` → `remarks` field name fixed
- [x] `showLoading()`/`hideLoading()` added to `viewClaim()`, `processClaim()`, `quickAction()`, `loadClaims()`
- [x] All `showToast` calls fixed to 3-arg `(title, message, type)`
- [x] Detail modal expanded: email, farm name, coverage amount, reviewer name, description block, remarks block

---

## PHASE 13 — Reports (`views/admin/reports.php`)

- [x] All stat cards → `api('GET', '/dashboard/stats')` (total, approved/active, pending, total_indemnity)
- [x] Status doughnut chart → from `/dashboard/stats` (active, pending, rejected, cancelled slices)
- [x] Claims by cause bar chart → derived from `/reports/claims` records, grouped by `incident_type`
- [x] Applications table → `api('GET', '/reports/policies')` — uses `records[]` with `farmer_name`, `farm_name`, `plan_name`, `area_hectares`, `coverage_amount`
- [x] Claims table → `api('GET', '/reports/claims')` — uses `records[]` with `farmer_name`, `farm_name`, `claim_number`, `incident_type`, `approved_amount`
- [x] **CSV Export** — two separate buttons: "Export Applications" → `fetch /reports/export/policies`, "Export Claims" → `fetch /reports/export/claims`; uses Bearer token + blob download (not `api()` wrapper)
- [x] `showLoading()`/`hideLoading()` wraps entire `loadReports()` + each `exportReport()` call
- [x] All `showToast` calls fixed to 3-arg `(title, message, type)`

---

## PHASE 14 — Admin Profile (`views/admin/admin-profile.php`)

- [x] On load, calls `api('GET', '/auth/me')` (was `/users/${user.id}`) to populate all form fields
- [x] Update profile → `api('PUT', '/users/${user.id}', { first_name, last_name, phone })` — re-fetches `/auth/me` after save, removed `localStorage.setItem()`, calls `initTopbarUser()`
- [x] Change password → `api('POST', '/auth/change-password', { current_password, new_password })` — clears fields + strength bar on success
- [x] Activity summary → `api('GET', '/dashboard/stats')` — `pol.total`, `clm.total`, `clm.total_approved_amount` (was reading wrong field `pay.total_payouts_disbursed`)
- [x] Password strength bar wired to `#new-pass` `oninput` via `updatePassStrength()`
- [x] `showLoading()`/`hideLoading()` added to `loadProfile()`, `saveProfile()`, `changePassword()`
- [x] All `showToast` calls fixed to 3-arg `(title, message, type)`

---

## PHASE 15 — User Management (`views/admin/user-management.php`) — NEW PAGE

- [x] Load all users → `api('GET', '/users?per_page=100')` — fixed from `perPage=500` (wrong param + server caps at 100)
- [x] **Create user** → `api('POST', '/users', { first_name, last_name, email, phone, role, status, password })`
- [x] **Edit user** → `api('PUT', '/users/{id}', { first_name, last_name, email, phone, role, status, password? })`
- [x] **Change Status** → `api('PUT', '/users/{id}/status', { status })` via status modal
- [x] **Deactivate user** → `api('DELETE', '/users/{id}')` with confirm modal (soft-deactivate)
- [x] `showLoading()`/`hideLoading()` added to `loadUsers()`, `saveUser()`, `applyStatusChange()`, delete confirm handler
- [x] Client-side search/filter/pagination already in place; stat cards count from loaded array

---

## PHASE 16 — Notifications

- [x] On every authenticated page load, call `api('GET', '/notifications/unread-count')` and show the count badge on the bell icon — auto-init via DOMContentLoaded in app.js
- [x] Clicking the bell opens a dropdown and calls `api('GET', '/notifications?per_page=10')`
- [x] "Mark all read" → `api('PUT', '/notifications/read-all')`
- [x] Single notification click → `api('PUT', '/notifications/{id}/read')` — re-fetches count badge after
- [x] "Clear all" → `api('DELETE', '/notifications/clear')` — reloads list after
- [x] Dropdown closes on outside click; unread items highlighted in blue

---

## PHASE 17 — Navigation & Routing Fixes

- [x] All `navigateTo('page.html')` calls must be updated to `navigateTo('page.php')` throughout all views
- [x] Update `app.js` redirect strings (`requireAuth`, `requireAdmin`, `logout`, `adminLogout`) from `.html` to `.php`
- [x] The `.htaccess` root rule currently routes everything to `index.html` — update to `index.php`
- [x] Admin pages blocked for farmer accounts — all 7 admin views set `$guardRole = 'admin'`; auth-guard.php rejects non-admin/agent roles server-side; JS `requireAdmin()` also checks role client-side
- [x] User pages require valid JWT — all 6 user views set `$guardRole = 'user'`; auth-guard.php validates JWT cookie and redirects to login if missing/expired

---

## PHASE 18 — Delete / Remove Legacy Files

- [x] Deleted `data/mockData.js`
- [x] No `<script src="...mockData.js">` tags found in any PHP view — already clean
- [x] Deleted `api/api-test.html`
- [x] Deleted `api/fix-passwords.php` and `api/test-db.php`
- [x] Removed hardcoded demo credentials from `views/admin/login.php` — removed `value="admin@lgu-stonino.gov.ph"` from email input and `value="admin2024"` from password input

---

## PHASE 19 — File Upload Wiring

- [x] `uploadClaimDocument()` in `file-claim.php` already uses `FormData` + `fetch()` with Bearer token — fully wired
- [x] Created `uploads/` and `uploads/claims/` directories at project root (writable by XAMPP)
- [x] Added `uploads/.htaccess` to block PHP execution in the uploads directory
- [x] Fixed `api/config/app.php` `UPLOAD_PATH` resolution — relative paths in `.env` now correctly anchor to project root instead of `api/` CWD
- [x] Fixed document link in `claim-verification.php` — was `/api/uploads/` (wrong), corrected to `/uploads/` (project root)
- [x] Frontend file type validation already in place: JPG, PNG, PDF only; max 10 MB enforced client-side

---

## PHASE 20 — Testing & QA

- [ ] Test full farmer signup → login → new application → view status → file claim → view claim flow
- [ ] Test admin login → review application → approve → verify farmer sees updated status
- [ ] Test admin reject flow with rejection reason
- [ ] Test password reset via email (configure SMTP in `.env`)
- [ ] Test CSV export downloads
- [ ] Test JWT expiry — expired token should redirect to login
- [ ] Test with multiple browser tabs (session consistency)
- [ ] Test mobile responsive layout on all converted pages

---

## QUICK REFERENCE — Files That Touch `localStorage` (All Must Be Replaced)

| File                                   | localStorage Keys Used                        |
| -------------------------------------- | --------------------------------------------- |
| `index.html`                           | `lgu_token`, `lgu_current_user`               |
| `views/user/signup.html`               | `lgu_users`                                   |
| `views/user/dashboard.html`            | `lgu_applications`, `lgu_claims`              |
| `views/user/new-application.html`      | `lgu_applications`                            |
| `views/user/my-applications.html`      | `lgu_applications`                            |
| `views/user/application-status.html`   | `lgu_applications`                            |
| `views/user/file-claim.html`           | `lgu_applications`, `lgu_claims`              |
| `views/user/profile.html`              | `lgu_current_user`, `lgu_users`               |
| `views/admin/login.html`               | `lgu_admin_logged_in`, `lgu_current_user`     |
| `views/admin/dashboard.html`           | `lgu_applications`, `lgu_claims`              |
| `views/admin/manage-applications.html` | `lgu_applications`                            |
| `views/admin/view-applications.html`   | `lgu_applications`                            |
| `views/admin/claim-verification.html`  | `lgu_claims`                                  |
| `views/admin/reports.html`             | `lgu_applications`, `lgu_claims`, `lgu_users` |
| `data/mockData.js`                     | all of the above (source)                     |

---

## Summary

| Phase | Task                                        | Priority            |
| ----- | ------------------------------------------- | ------------------- |
| 0     | Database setup                              | CRITICAL — do first |
| 1     | Rename all HTML → PHP + shared includes     | HIGH                |
| 2     | Wire authentication (login, signup, logout) | HIGH                |
| 3–8   | User portal API integration (all 6 pages)   | HIGH                |
| 9–14  | Admin portal API integration (all 6 pages)  | HIGH                |
| 15    | User management page (new)                  | MEDIUM              |
| 16    | Notifications bell/dropdown                 | MEDIUM              |
| 17    | Navigation routing fixes (.html → .php)     | HIGH                |
| 18    | Delete legacy files & mock data             | MEDIUM              |
| 19    | File upload wiring                          | MEDIUM              |
| 20    | End-to-end testing & QA                     | HIGH                |
