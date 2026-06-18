# Version Control — Web-Based Crop Insurance System

## Rollout Schedule

| Version | Role   | Feature Unlocked               | Pages Still Gated |
|---------|--------|--------------------------------|-------------------|
| v1.00   | Public | Login (Farmer + Admin), Signup, Forgot Password | All below |
| v1.01   | Admin  | Admin Dashboard                | v1.02–v1.13 |
| v1.02   | Admin  | View Applications              | v1.03–v1.13 |
| v1.03   | Admin  | Manage Applications            | v1.04–v1.13 |
| v1.04   | Admin  | Claim Verification             | v1.05–v1.13 |
| v1.05   | Admin  | Reports                        | v1.06–v1.13 |
| v1.06   | Admin  | User Management                | v1.07–v1.13 |
| v1.07   | Admin  | Admin Profile                  | v1.08–v1.13 |
| v1.08   | User   | Farmer Dashboard               | v1.09–v1.13 |
| v1.09   | User   | New Application                | v1.10–v1.13 |
| v1.10   | User   | My Applications                | v1.11–v1.13 |
| v1.11   | User   | Application Status             | v1.12–v1.13 |
| v1.12   | User   | File a Claim                   | v1.13 |
| v1.13   | User   | Farmer Profile *(Full System)* | — |

---

## Pages Per Version

### v1.00 — Ungated (always accessible)
- `index.php` — Farmer login / landing page
- `views/admin/login.php` — Admin login
- `views/user/signup.php` — Farmer registration
- `views/user/forgot-password.php` — Password recovery

### v1.01 — Unlock Admin Dashboard
- Remove gate from `views/admin/dashboard.php`

### v1.02 — Unlock View Applications
- Remove gate from `views/admin/view-applications.php`

### v1.03 — Unlock Manage Applications
- Remove gate from `views/admin/manage-applications.php`

### v1.04 — Unlock Claim Verification
- Remove gate from `views/admin/claim-verification.php`

### v1.05 — Unlock Reports
- Remove gate from `views/admin/reports.php`

### v1.06 — Unlock User Management
- Remove gate from `views/admin/user-management.php`

### v1.07 — Unlock Admin Profile
- Remove gate from `views/admin/admin-profile.php`

### v1.08 — Unlock Farmer Dashboard
- Remove gate from `views/user/dashboard.php`

### v1.09 — Unlock New Application
- Remove gate from `views/user/new-application.php`

### v1.10 — Unlock My Applications
- Remove gate from `views/user/my-applications.php`

### v1.11 — Unlock Application Status
- Remove gate from `views/user/application-status.php`

### v1.12 — Unlock File a Claim
- Remove gate from `views/user/file-claim.php`

### v1.13 — Unlock Farmer Profile (Full System)
- Remove gate from `views/user/profile.php`

---

## Under Construction Strategy

Every gated page starts with:

```php
<?php require_once '../../components/under-construction.php'; ?>
```

`components/under-construction.php` defines `CURRENT_VERSION`, renders a full styled page with a hard-hat icon and version badge, then calls `exit` — so none of the real page content ever runs.

To unlock a page for a given version:
1. Remove the `require_once` gate line from that page.
2. Update `define('CURRENT_VERSION', ...)` in `components/under-construction.php` to the new version.
3. Commit, tag, and push (see commands below).

---

## Git Commands Per Version

```bash
# Stage the unlocked page + updated under-construction component
git add views/<role>/<page>.php components/under-construction.php

# Commit
git commit -m "feat: implement vX.XX - unlock [Feature Name]"

# Tag and push
git tag vX.XX
git push origin main
git push origin vX.XX
```

---

## How Git Tags Work

Each `git tag vX.XX` creates a permanent, immutable snapshot of the repository at that exact commit. Even if later commits change the code, the tag always points to the same state — so your professor or client can check out any version with:

```bash
git checkout vX.XX
```

Tags are pushed separately from commits:

```bash
git push origin vX.XX   # push a single tag
git push origin --tags  # push all tags at once
```

---

## GitHub Release Tags

| Version | Tag Name | Commit Hash |
|---------|----------|-------------|
| v1.00   | v1.00    |             |
| v1.01   | v1.01    |             |
| v1.02   | v1.02    |             |
| v1.03   | v1.03    |             |
| v1.04   | v1.04    |             |
| v1.05   | v1.05    |             |
| v1.06   | v1.06    |             |
| v1.07   | v1.07    |             |
| v1.08   | v1.08    |             |
| v1.09   | v1.09    |             |
| v1.10   | v1.10    |             |
| v1.11   | v1.11    |             |
| v1.12   | v1.12    |             |
| v1.13   | v1.13    |             |

Fill commit hashes after all versions are tagged using:

```bash
git tag | sort | xargs -I{} git log -1 --format="{} %H" {}
```

---

## When a Professor or Client Requests Changes After a Presentation

```bash
# Fix on main first
git checkout main
git add <changed-files>
git commit -m "feat: update [page] per feedback"
git push origin main

# Delete the old tag and re-create it pointing to the new commit
git tag -d vX.XX
git push origin :refs/tags/vX.XX
git tag vX.XX
git push origin vX.XX
```

This moves the tag forward to your updated commit while keeping the same version label visible on GitHub.
