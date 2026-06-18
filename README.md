# LGU Sto. Niño Web-Based Crop Insurance System

A full-stack PHP web application for managing crop insurance for farmers under the LGU Sto. Niño jurisdiction. The system handles user registration, farm management, insurance policy applications, claims processing, payments, and reporting.

---

## Tech Stack

| Layer          | Technology                             |
| -------------- | -------------------------------------- |
| Frontend       | PHP views + vanilla JavaScript         |
| Backend        | PHP 8+ (custom REST API, no framework) |
| Database       | MySQL (via PDO)                        |
| Authentication | JWT (JSON Web Tokens)                  |
| Email          | PHP `mail()` / PHPMailer (SMTP)        |
| Server         | Apache (XAMPP)                         |

---

## Features

- **User Authentication** — Register, login, forgot/reset password, role-based access (admin / farmer)
- **Farm Management** — Add and manage farm records
- **Policy Applications** — Apply for crop insurance coverage; admin review, approval, and rejection
- **Claims Filing** — File and track claims with document uploads
- **Payments** — Premium payments and claim payouts
- **Notifications** — In-app notification system with unread count
- **Reports & Dashboard** — Charts and exports for claims, policies, and payments
- **User Management** (admin) — Create, edit, and toggle user status
- **Document Uploads** — Attach images/PDFs to policies and claims

---

## Project Structure

```
web-based-crop-insurance/
├── api/
│   ├── bootstrap.php           # API initialization (env, config, helpers, middleware)
│   ├── index.php               # API entry point & router
│   ├── config/
│   │   ├── app.php             # App constants from .env
│   │   ├── database.php        # PDO singleton
│   │   └── env.php             # .env loader
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── ClaimController.php
│   │   ├── FarmController.php
│   │   ├── NotificationController.php
│   │   ├── PaymentController.php
│   │   ├── PlanController.php
│   │   ├── PolicyController.php
│   │   ├── ReportController.php
│   │   └── UserController.php
│   ├── helpers/
│   │   ├── cors.php
│   │   ├── jwt.php
│   │   ├── mailer.php
│   │   ├── notification.php
│   │   ├── response.php
│   │   ├── security.php
│   │   ├── upload.php
│   │   └── validation.php
│   ├── middleware/
│   │   ├── AuthMiddleware.php
│   │   ├── RateLimitMiddleware.php
│   │   └── RoleMiddleware.php
│   └── models/
│       ├── ClaimModel.php
│       ├── FarmModel.php
│       ├── NotificationModel.php
│       ├── PaymentModel.php
│       ├── PlanModel.php
│       ├── PolicyModel.php
│       ├── ReportModel.php
│       └── UserModel.php
├── assets/
│   ├── css/style.css
│   ├── js/app.js
│   └── images/
├── database/
│   ├── schema.sql
│   ├── seeders.sql
│   └── migration_*.sql
├── docs/                       # Development docs and checklists
├── includes/
│   ├── auth-guard.php          # Redirect unauthenticated users
│   ├── admin-sidebar.php
│   ├── user-sidebar.php
│   ├── head.php
│   ├── topbar.php
│   └── toast.php
├── uploads/                    # User-uploaded documents
├── views/
│   ├── admin/
│   │   ├── login.php
│   │   ├── dashboard.php
│   │   ├── view-applications.php
│   │   ├── manage-applications.php
│   │   ├── claim-verification.php
│   │   ├── reports.php
│   │   ├── user-management.php
│   │   └── admin-profile.php
│   └── user/
│       ├── signup.php
│       ├── forgot-password.php
│       ├── dashboard.php
│       ├── new-application.php
│       ├── my-applications.php
│       ├── application-status.php
│       ├── file-claim.php
│       └── profile.php
├── .env                        # Local environment variables (not committed)
├── .env.example                # Environment variable template
├── .htaccess                   # URL rewriting + .env protection
└── index.php                   # Root entry point (login page)
```

---

## Getting Started

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) (PHP 8+, Apache, MySQL)
- Git

### Installation

1. **Clone the repository** into your XAMPP `htdocs` folder:

   ```bash
   git clone <repo-url> C:/xampp/htdocs/web-based-crop-insurance
   ```

2. **Create the database** in phpMyAdmin or MySQL CLI:

   ```sql
   CREATE DATABASE crop_insurance_db;
   ```

3. **Import the schema and seed data:**

   ```bash
   mysql -u root crop_insurance_db < database/schema.sql
   mysql -u root crop_insurance_db < database/seeders.sql
   ```

4. **Copy and configure the environment file:**

   ```bash
   cp .env.example .env
   ```

   Edit `.env` with your local settings (see [Environment Variables](#environment-variables)).

5. **Enable Apache `mod_rewrite`** in `httpd.conf` if not already enabled:

   ```apache
   LoadModule rewrite_module modules/mod_rewrite.so
   ```

6. **Start XAMPP** (Apache + MySQL), then open:
   ```
   http://localhost/web-based-crop-insurance
   ```

---

## Environment Variables

Copy `.env.example` to `.env` and fill in your values:

```env
# Application
APP_NAME="Web-Based Crop Insurance"
APP_ENV=development
APP_URL=http://localhost/web-based-crop-insurance
APP_DEBUG=true
APP_TIMEZONE=Asia/Manila

# Database
DB_HOST=localhost
DB_PORT=3306
DB_NAME=crop_insurance_db
DB_USER=root
DB_PASS=

# JWT
JWT_SECRET=your_random_secret_here
JWT_EXPIRY=86400

# File Uploads
UPLOAD_MAX_SIZE=5242880
UPLOAD_PATH=uploads/
ALLOWED_TYPES=jpg,jpeg,png,pdf

# Email (SMTP via PHPMailer)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_FROM=noreply@cropinsurance.ph
MAIL_FROM_NAME="Crop Insurance System"
```

> The `.env` file is blocked from web access via `.htaccess` and should never be committed.

---

## API Reference

All API endpoints are under `/api/` and return JSON.

| Method         | Endpoint                          | Description                  |
| -------------- | --------------------------------- | ---------------------------- |
| GET            | `/api/health`                     | API health check             |
| POST           | `/api/auth/register`              | Register a new user          |
| POST           | `/api/auth/login`                 | Login                        |
| POST           | `/api/auth/logout`                | Logout                       |
| POST           | `/api/auth/forgot-password`       | Request password reset email |
| POST           | `/api/auth/reset-password`        | Reset password with token    |
| POST           | `/api/auth/change-password`       | Change password              |
| GET            | `/api/auth/me`                    | Get current user info        |
| GET/POST       | `/api/users`                      | List / create users          |
| GET/PUT/DELETE | `/api/users/{id}`                 | Get / update / delete user   |
| PUT            | `/api/users/{id}/status`          | Toggle user status           |
| GET/POST       | `/api/farms`                      | List / create farms          |
| GET/PUT/DELETE | `/api/farms/{id}`                 | Farm detail operations       |
| GET/POST       | `/api/plans`                      | List / create coverage plans |
| GET/POST       | `/api/policies`                   | List / apply for policies    |
| PUT            | `/api/policies/{id}/approve`      | Approve a policy             |
| PUT            | `/api/policies/{id}/reject`       | Reject a policy              |
| POST           | `/api/policies/{id}/documents`    | Upload policy documents      |
| GET/POST       | `/api/claims`                     | List / file claims           |
| PUT            | `/api/claims/{id}/status`         | Update claim status          |
| POST           | `/api/claims/{id}/documents`      | Upload claim documents       |
| GET            | `/api/payments`                   | List payments                |
| POST           | `/api/payments/premium`           | Record premium payment       |
| POST           | `/api/payments/payout`            | Record claim payout          |
| GET            | `/api/notifications`              | List notifications           |
| GET            | `/api/notifications/unread-count` | Unread count                 |
| PUT            | `/api/notifications/read-all`     | Mark all as read             |
| GET            | `/api/dashboard/stats`            | Dashboard statistics         |
| GET            | `/api/reports/claims`             | Claims report                |
| GET            | `/api/reports/policies`           | Policies report              |
| GET            | `/api/reports/export/{type}`      | Export report                |

---

## Security

- JWT tokens stored in `sessionStorage` on the client; validated server-side on every protected request
- `.env` blocked from web access via `.htaccess`
- `uploads/` directory blocks direct PHP execution
- Rate limiting middleware on API routes
- Role-based access control (admin / user) enforced via `RoleMiddleware`
- `auth-guard.php` included at the top of all protected views to redirect unauthenticated users
- Directory listing disabled (`Options -Indexes`) on all directories

---

## Default Credentials (Seeded)

All seeded accounts use the same password: **`Password@123`**

| Role   | Email                      |
| ------ | -------------------------- |
| Admin  | `admin@cropinsurance.ph`   |
| Agent  | `agent1@cropinsurance.ph`  |
| Agent  | `agent2@cropinsurance.ph`  |
| Farmer | `farmer1@cropinsurance.ph` |
| Farmer | `farmer2@cropinsurance.ph` |
| Farmer | `farmer3@cropinsurance.ph` |

> Change all default passwords before deploying to any shared or production environment.

---

## License

For academic/LGU use only.
