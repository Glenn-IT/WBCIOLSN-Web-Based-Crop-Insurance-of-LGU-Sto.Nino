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
| v2.00   | Admin  | Admin Dashboard, Admin Profile (re-gated snapshot) | All other pages |
| v3.00   | Admin + User | User Management, Farmer Dashboard (added to v2.00 baseline) | All other pages |
| v3.10   | Admin + User | All remaining pages unlocked *(Full System)* | — |
| v4.00   | Admin + User | View Applications, New Application (added to v3.00 baseline) | All other pages |

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

### v2.00 — New rollout: re-gate everything except v1.00 + Admin Dashboard + Admin Profile
Built on top of `main` (which includes fixes/features added after v1.13, e.g. security questions,
under-construction caching fix). Re-adds the under-construction gate to every page that isn't
part of v1.00 or the two carried-over admin pages:
- Re-gate `views/admin/view-applications.php`
- Re-gate `views/admin/manage-applications.php`
- Re-gate `views/admin/claim-verification.php`
- Re-gate `views/admin/reports.php`
- Re-gate `views/admin/user-management.php`
- Re-gate `views/user/dashboard.php`
- Re-gate `views/user/new-application.php`
- Re-gate `views/user/my-applications.php`
- Re-gate `views/user/application-status.php`
- Re-gate `views/user/file-claim.php`
- Re-gate `views/user/profile.php`
- Stay unlocked: `views/admin/dashboard.php`, `views/admin/admin-profile.php`
- Stay unlocked (v1.00 baseline): `index.php`, `views/admin/login.php`, `views/user/signup.php`, `views/user/forgot-password.php`

### v3.00 — Unlock User Management + Farmer Dashboard
Built on top of the v2.00 snapshot. Adds two more unlocked pages, everything else stays gated:
- Remove gate from `views/admin/user-management.php`
- Remove gate from `views/user/dashboard.php`
- Stay unlocked (carried over): `views/admin/dashboard.php`, `views/admin/admin-profile.php`
- Stay unlocked (v1.00 baseline): `index.php`, `views/admin/login.php`, `views/user/signup.php`, `views/user/forgot-password.php`
- Stay gated: `views/admin/view-applications.php`, `views/admin/manage-applications.php`, `views/admin/claim-verification.php`, `views/admin/reports.php`, `views/user/new-application.php`, `views/user/my-applications.php`, `views/user/application-status.php`, `views/user/file-claim.php`, `views/user/profile.php`

### v3.10 — Unlock all remaining pages (Full System)
Built on top of the v3.00 snapshot. Removes the under-construction gate from every
remaining page, so the entire system is fully accessible:
- Remove gate from `views/admin/view-applications.php`
- Remove gate from `views/admin/manage-applications.php`
- Remove gate from `views/admin/claim-verification.php`
- Remove gate from `views/admin/reports.php`
- Remove gate from `views/user/new-application.php`
- Remove gate from `views/user/my-applications.php`
- Remove gate from `views/user/application-status.php`
- Remove gate from `views/user/file-claim.php`
- Remove gate from `views/user/profile.php`
- Stay unlocked (carried over): `views/admin/dashboard.php`, `views/admin/admin-profile.php`, `views/admin/user-management.php`, `views/user/dashboard.php`
- Stay unlocked (v1.00 baseline): `index.php`, `views/admin/login.php`, `views/user/signup.php`, `views/user/forgot-password.php`

### v4.00 — Unlock View Applications + New Application
Built on top of the v3.00 snapshot (not v3.10 — the full-system unlock is rolled back). Adds two
more unlocked pages, everything else stays/goes gated again:
- Remove gate from `views/admin/view-applications.php`
- Remove gate from `views/user/new-application.php`
- Stay unlocked (carried over): `views/admin/dashboard.php`, `views/admin/admin-profile.php`, `views/admin/user-management.php`, `views/user/dashboard.php`
- Stay unlocked (v1.00 baseline): `index.php`, `views/admin/login.php`, `views/user/signup.php`, `views/user/forgot-password.php`
- Re-gate (rolled back from v3.10): `views/admin/manage-applications.php`, `views/admin/claim-verification.php`, `views/admin/reports.php`, `views/user/my-applications.php`, `views/user/application-status.php`, `views/user/file-claim.php`, `views/user/profile.php`

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
| v1.00   | v1.00    | 9a397362f6f5e875b4316cc1ed7a0808b7d5650c |
| v1.01   | v1.01    | 8cdd0507e678308e5b0fdd5acfb7c8c83159b801 |
| v1.02   | v1.02    | bb013a3b029b2e68d67480eb28bb730674071fb9 |
| v1.03   | v1.03    | 5e2211452553a2e8f25e492ff05f9223b585cdec |
| v1.04   | v1.04    | f42f31daf95fedee0e5952f90ab9426f3782a4d2 |
| v1.05   | v1.05    | 301d5b22de9d2b2ff514e24b923f05826841f465 |
| v1.06   | v1.06    | 259d5ea457dbd22a3adde35b51882d0a666ca80f |
| v1.07   | v1.07    | fff0870cb42d42b5faa0d88e3c57cde3036651f1 |
| v1.08   | v1.08    | 7d7b6cfa7be990030eb6110833f975415fb078c4 |
| v1.09   | v1.09    | c4bd922e18a0c06e0fb3bab51ac3ff35c3e08b65 |
| v1.10   | v1.10    | d023a10665055e9bf62efa5ed5c73a536129d481 |
| v1.11   | v1.11    | fc99fbf8f5a0c1b3b8f9f385e3ec960549301a9f |
| v1.12   | v1.12    | b64c5ab29642f102d749aef26670ab0ac03b2071 |
| v1.13   | v1.13    | e63714d94852abde6a3ab1ea7d0b168d5a9f33f3 |
| v2.00   | v2.00    | 43293b18e09cbd100db0c227549ba4423e705b17 |
| v3.00   | v3.00    | 84beae93a22b87dc96550d2a5e71c4935e0d12bd |
| v3.10   | v3.10    | 6d45ffb5a067dcb9cb59d0c868ae0d1308e8ed58 |
| v4.00   | v4.00    | 2eaec727c7fb6866efe2d3481644bf60f1fc136b |

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
