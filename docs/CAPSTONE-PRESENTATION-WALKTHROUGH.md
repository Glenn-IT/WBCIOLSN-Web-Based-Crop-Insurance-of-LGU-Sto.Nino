# WBCIOLSN — Capstone Defense Presentation Walkthrough & Panelist Demonstration Guide
<!-- System: Web-Based Crop Insurance of LGU Sto. Niño (WBCIOLSN) -->
<!-- Target Audience: Capstone Defense Panelists, Advisers, Deans, and Evaluators -->

---

## 🧭 Executive Summary & Timing Strategy

| Phase | Section | Recommended Duration | Primary Interface |
| :--- | :--- | :--- | :--- |
| **Phase 1** | Project Rationale & Agricultural Context | 1.5 mins | Title Slide / [index.php](file:///C:/xampp/htdocs/web-based-crop-insurance/index.php) |
| **Phase 2** | Architecture, RBAC & Security Baseline | 1.0 min | [README.md](file:///C:/xampp/htdocs/web-based-crop-insurance/README.md) / [api/bootstrap.php](file:///C:/xampp/htdocs/web-based-crop-insurance/api/bootstrap.php) |
| **Phase 3** | Public Gateway & LGU Sto. Niño Identity | 1.0 min | [index.php](file:///C:/xampp/htdocs/web-based-crop-insurance/index.php) |
| **Phase 4** | Farmer Onboarding & Security Trapping | 1.0 min | [views/user/signup.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/user/signup.php) |
| **Phase 5** | Farmer Command Dashboard & Policy Overview | 1.0 min | [views/user/dashboard.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/user/dashboard.php) |
| **Phase 6** | 4-Step Policy Application & OpenStreetMap Geo-Tagging | 2.0 mins | [views/user/new-application.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/user/new-application.php) |
| **Phase 7** | Visual Damage Assessment & 5-Photo Proof Trapping | 1.5 mins | [views/user/new-application.php#L262-L287](file:///C:/xampp/htdocs/web-based-crop-insurance/views/user/new-application.php#L262-L287) |
| **Phase 8** | Real-Time Policy Tracking & Status Lifecycle | 1.0 min | [views/user/my-applications.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/user/my-applications.php) & [views/user/application-status.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/user/application-status.php) |
| **Phase 9** | Crop Damage Claim Filing & Loss Quantification | 1.0 min | [views/user/file-claim.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/user/file-claim.php) |
| **Phase 10** | Admin Command Center & Agricultural KPIs | 1.0 min | [views/admin/dashboard.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/admin/dashboard.php) |
| **Phase 11** | Policy Triage & 3-Tier Verification Matrix | 1.5 mins | [views/admin/manage-applications.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/admin/manage-applications.php) |
| **Phase 12** | Claim Adjudication, Indemnity & Claim Slip Printing | 1.5 mins | [views/admin/claim-verification.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/admin/claim-verification.php) |
| **Phase 13** | Multi-Channel Notifications (PhilSMS SMS + Email Alerts) | 1.0 min | [views/admin/sms-logs.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/admin/sms-logs.php) & [api/helpers/notification.php](file:///C:/xampp/htdocs/web-based-crop-insurance/api/helpers/notification.php) |
| **Phase 14** | Municipal User Governance & Role Management | 1.0 min | [views/admin/user-management.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/admin/user-management.php) |
| **Phase 15** | Official LGU Agricultural Reports & Custom Signatories | 1.0 min | [views/admin/reports.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/admin/reports.php) |
| **Phase 16** | System Audit Trail & Transition to Panel Q&A | 0.5 min | [views/admin/dashboard.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/admin/dashboard.php) |
| **Total** | **Full System Defense Presentation** | **~15.0 mins** | — |

---

## 🛠️ Pre-Defense Staging & Credentials Setup

Before starting the defense presentation, prepare your demonstration workstation:

1. **Browser Setup**:
   * **Window 1 (Main Browser):** Logged in as **Admin / MAO Officer** or **Agent**.
   * **Window 2 (Incognito / Private Window):** Ready for the **Farmer** live demo. This eliminates logging in and out during role transitions and enables real-time dual-screen status synchronization.
2. **Standard Accounts**:
   * **Admin Account:** `admin@cropinsurance.ph` | Password: `Password@123`
   * **Agent Accounts:** `agent1@cropinsurance.ph`, `agent2@cropinsurance.ph` | Password: `Password@123`
   * **Farmer Accounts:** `farmer1@cropinsurance.ph`, `farmer2@cropinsurance.ph`, `farmer3@cropinsurance.ph` | Password: `Password@123`
3. **Database Seed Setup**:
   * Initial database schema available at [`database/schema.sql`](file:///C:/xampp/htdocs/web-based-crop-insurance/database/schema.sql) and seed data at [`database/seeders.sql`](file:///C:/xampp/htdocs/web-based-crop-insurance/database/seeders.sql).

### 👥 Seeded Demonstration Accounts

| # | Name | Email (`Password@123`) | Role | Phone | Location / Farm | Status |
| :- | :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | **System Admin** | `admin@cropinsurance.ph` | Municipal Admin | `09000000001` | MAO Sto. Niño HQ | **Active** |
| 2 | **Maria Santos** | `agent1@cropinsurance.ph` | Agricultural Agent | `09111111111` | Field Inspection Unit | **Active** |
| 3 | **Jose Reyes** | `agent2@cropinsurance.ph` | Agricultural Agent | `09222222222` | Claims Verifier | **Active** |
| 4 | **Pedro Dela Cruz** | `farmer1@cropinsurance.ph` | Farmer | `09333333333` | Brgy. Maligaya / Rice (2.5 ha) | **Active** (Has Claims & Policies) |
| 5 | **Luisa Garcia** | `farmer2@cropinsurance.ph` | Farmer | `09444444444` | Brgy. San Isidro / Corn (3.75 ha) | **Active** (Policy Active, Claim Under Review) |
| 6 | **Ramon Flores** | `farmer3@cropinsurance.ph` | Farmer | `09555555555` | Brgy. Rizal / Sugarcane (5.0 ha) | **Active** (Pending Application Ready for Triage) |

### 🌾 Seeded Insurance Coverage Plans

| Plan Name | Coverage Type | Coverage % | Premium Rate | Max Coverage | Duration | Target Risk |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Basic Flood Cover** | `flood` | 70% | 5.0% | ₱50,000.00 | 6 Months | Heavy Monsoon / Overflow |
| **Natural Disaster Plan** | `natural_disaster` | 80% | 6.5% | ₱100,000.00 | 6 Months | Typhoons, Flash Floods, Quakes |
| **Pest & Disease Shield** | `pest_disease` | 75% | 4.5% | ₱75,000.00 | 6 Months | Planthopper, Stem Borer, Blight |
| **Drought Protection** | `drought` | 70% | 5.5% | ₱60,000.00 | 6 Months | El Niño / Prolonged Dry Spells |
| **Comprehensive Plan** | `comprehensive` | 90% | 8.5% | ₱200,000.00 | 12 Months | All Insurable Agricultural Hazards |

---

## 🎬 Step-by-Step Presentation Script (From First to Last)

---

### Step 1: Opening & Agricultural Problem Statement
* **Screen Display:** Title Slide or [index.php](file:///C:/xampp/htdocs/web-based-crop-insurance/index.php) hero section
* **Estimated Time:** 1.5 minutes
* **Screen Action:** Present the title slide featuring the official seals of the Municipal Agriculture Office and the Municipality of Sto. Niño ([img/Agri-Sto-Logo.png](file:///C:/xampp/htdocs/web-based-crop-insurance/img/Agri-Sto-Logo.png) & [img/Municipality-logo.png](file:///C:/xampp/htdocs/web-based-crop-insurance/img/Municipality-logo.png)).
* **🗣️ Verbal Script:**
  > *"Good morning, honorable panel members, project advisers, and guests. Today, we are proud to present **WBCIOLSN — the Web-Based Crop Insurance of LGU Sto. Niño**.*
  >
  > *Sto. Niño, Cagayan is a predominantly agricultural municipality situated along the fertile Cagayan Valley river basin. While its lands yield high volumes of palay, corn, and commercial crops, its geographical location exposes local farmers to recurrent typhoons, seasonal flooding, prolonged drought, and pest outbreaks.*
  >
  > *Historically, crop insurance enrollment and calamity indemnification through traditional paper processes faced severe challenges: farmers had to travel long distances from remote barangays to the municipal hall, paper claim dossiers were frequently delayed or lost, field validation lacked geospatial verification, and claim payouts took months to process.*
  >
  > *WBCIOLSN directly overcomes these bottlenecks by establishing a modern, centralized, and transparent digital crop insurance platform built specifically for the Municipal Agriculture Office and local farmers of Sto. Niño."*

---

### Step 2: System Architecture & Technical Baseline
* **Screen Display:** Architecture Diagram or [README.md](file:///C:/xampp/htdocs/web-based-crop-insurance/README.md) / [api/bootstrap.php](file:///C:/xampp/htdocs/web-based-crop-insurance/api/bootstrap.php)
* **Estimated Time:** 1.0 minute
* **Screen Action:** Highlight the clean software architecture, database design, and defensive engineering principles.
* **🗣️ Verbal Script:**
  > *"Under the hood, WBCIOLSN is engineered on a lightweight, modular PHP 8+ custom RESTful API architecture backed by MySQL and responsive frontend views:
  >
  > 1. **Role-Based Access Control (RBAC):** Strict operational segregation among `Admin`, `Agent`, and `Farmer` roles enforced via [RoleMiddleware.php](file:///C:/xampp/htdocs/web-based-crop-insurance/api/middleware/RoleMiddleware.php) and [auth-guard.php](file:///C:/xampp/htdocs/web-based-crop-insurance/includes/auth-guard.php).
  > 2. **100% Prepared Statements:** All database interactions utilize PDO with parameter binding via [database.php](file:///C:/xampp/htdocs/web-based-crop-insurance/api/config/database.php), completely neutralizing SQL Injection vulnerabilities.
  > 3. **Session & Token Security:** Secure JWT token handling, bcrypt password hashing (`PASSWORD_BCRYPT`), and input sanitization (`sanitize()`, `guardSqlInjection()`).
  > 4. **No-Cost Interactive Geospatial Mapping:** Integrated Leaflet.js with OpenStreetMap, delivering satellite/map geotagging without requiring recurring proprietary Google Maps API billing.
  > 5. **Hybrid Notification Pipeline:** Automated transactional notifications using PHPMailer SMTP alongside the **PhilSMS Gateway API** to reach rural farmers even when they have no mobile internet data."*

---

### Step 3: Public Gateway & Municipal Agricultural Identity
* **Screen Display:** [index.php](file:///C:/xampp/htdocs/web-based-crop-insurance/index.php)
* **Estimated Time:** 1.0 minute
* **Screen Action:** Walk through the login portal, highlighting the municipal seal, agricultural brand identity, mobile responsiveness, and quick navigation links.
* **🗣️ Verbal Script:**
  > *"Our public portal greets users with the official seal of LGU Sto. Niño. Built mobile-first, farmers accessing the portal from their smartphones in the barangays experience an intuitive, responsive interface with easy access to application forms, status tracking, and account recovery."*

---

### Step 4: Farmer Onboarding & Security Trapping
* **Screen Display:** [views/user/signup.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/user/signup.php)
* **Estimated Time:** 1.0 minute
* **Screen Action:**
  1. Open the registration form.
  2. Demonstrate the live password strength validator (minimum 8 characters, uppercase, lowercase, number, and special character).
  3. Show the security question selection for self-service account recovery.
  4. Note Philippine mobile number formatting (`09XXXXXXXXX`) essential for PhilSMS text broadcasts.
* **🗣️ Verbal Script:**
  > *"During registration, the system enforces strong password standards and requests a valid mobile number and security question. This phone number serves as the direct pipeline for PhilSMS alerts whenever their insurance policies or calamity claims undergo status changes."*

---

### Step 5: Farmer Command Dashboard & Policy Overview
* **Screen Display:** Log in as `farmer1@cropinsurance.ph` in Window 2 -> [views/user/dashboard.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/user/dashboard.php)
* **Estimated Time:** 1.0 minute
* **Screen Action:** Point out the farmer metric tiles: *Active Policies, Total Coverage Amount, Pending Claims, and Approved Indemnity*. Show recent notifications in the top bar.
* **🗣️ Verbal Script:**
  > *"Once logged in, the farmer is presented with their personal agricultural command dashboard. Farmers immediately see their active policies, total insured peso value, submitted claims, and in-app notifications, removing any ambiguity regarding their insurance coverage standing."*

---

### Step 6: 4-Step Crop Insurance Application & OpenStreetMap Geo-Tagging
* **Screen Display:** [views/user/new-application.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/user/new-application.php)
* **Estimated Time:** 2.0 minutes
* **Screen Action:**
  1. Navigate to **New Application**. Show the 4-step progress stepper:
     * **Step 1: Basic Info** — Select Application Type (New/Renewal), Farmer Category (*Small Farmer, Commercial Farmer, ARB, Fisherfolk*), and Crop Type (*Rice, Corn, Sugarcane, etc.*). Click *Next*.
     * **Step 2: Farm Information** — Enter location, area in hectares, land category (*Irrigated, Rain-fed, Upland*), tenurial status (*Owner, Lessee, Tenant, Caretaker*), and planting/harvest dates.
     * **Interactive Geotag Map:** Scroll to the Leaflet OpenStreetMap canvas. Click on the map to pin the farm location. Point out the auto-populated latitude and longitude badges.
* **🗣️ Verbal Script:**
  > *"Here is one of our primary innovations: our 4-Step Crop Insurance Application Wizard.
  >
  > *In Step 2, instead of vague paper descriptions of farm boundaries, farmers simply tap on our interactive Leaflet OpenStreetMap to drop an exact geolocation pin on their parcel. The system stores exact GPS coordinates, empowering agricultural inspectors to verify farm locations with pinpoint accuracy without requiring expensive proprietary GIS software."*

---

### Step 7: Visual Damage Assessment & 5-Photo Proof Trapping
* **Screen Display:** [views/user/new-application.php#L206-L287](file:///C:/xampp/htdocs/web-based-crop-insurance/views/user/new-application.php#L206-L287)
* **Estimated Time:** 1.5 minutes
* **Screen Action:**
  1. Advance to **Step 3: Damage Report**.
  2. Select Cause of Damage (*Typhoon, Flood, Pest/Disease, Drought, Fire*).
  3. Input visual damage percentage (e.g., `75%`) and demonstrate the automated financial damage calculator.
  4. Scroll down to **Photo Evidence**.
  5. Attempt to proceed without adding photos or with fewer than 5 photos to trigger the validation alert: `⚠️ Please add at least 5 photos as evidence of damage.`
  6. Drag and drop 5 sample photos into the photo grid to show live image previews and counter `(5 / min 5 photos)`.
* **🗣️ Verbal Script:**
  > *"To safeguard the municipal insurance fund against spurious or fraudulent declarations, Step 3 enforces strict visual damage trapping:
  >
  > *The system mandates that applicants upload a minimum of **5 geo-verifiable photographs** documenting the actual crop damage before the application can proceed. Each file is verified for MIME type, size-constrained to prevent server denial-of-service, and previewed immediately."*

---

### Step 8: Coverage Plan Selection, Financial Calculation & Farmer Consent
* **Screen Display:** [views/user/new-application.php#L295-L370](file:///C:/xampp/htdocs/web-based-crop-insurance/views/user/new-application.php#L295-L370)
* **Estimated Time:** 1.0 minute
* **Screen Action:**
  1. Proceed to **Step 4: Coverage & Consent**.
  2. Select an insurance plan (e.g., *Natural Disaster Plan*). Show the dynamic info tile: *Coverage Type, Coverage %, Max Coverage*.
  3. Enter desired coverage amount.
  4. Attach Government/Valid ID.
  5. Check legal consent and declaration checkboxes and click **"Submit Application"**.
* **🗣️ Verbal Script:**
  > *"In Step 4, the farmer selects from municipal coverage plans with transparent premium calculations. Upon agreeing to the legal consent and accuracy terms, the application is stored and a unique tracking number (`POL-2026-XXXXXX`) is generated."*

---

### Step 9: Real-Time Application Tracking & Status Lifecycle
* **Screen Display:** [views/user/my-applications.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/user/my-applications.php) & [views/user/application-status.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/user/application-status.php)
* **Estimated Time:** 1.0 minute
* **Screen Action:**
  1. Open **My Applications** to show the newly submitted application marked with a yellow `Pending` badge.
  2. Click **View Details** to open the modal containing full details, map coordinates, and uploaded evidence.
  3. Navigate to **Application Status** to highlight the real-time visual milestone timeline (*Submitted -> Under Review -> Farm Inspected -> Approved / Active*).
* **🗣️ Verbal Script:**
  > *"Farmers no longer experience anxiety or make repeated trips to the Municipal Agriculture Office just to ask if their paperwork was touched. The application status tracker provides an end-to-end visual timeline with admin remarks visible at every milestone."*

---

### Step 10: Crop Damage Claim Filing & Loss Quantification
* **Screen Display:** [views/user/file-claim.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/user/file-claim.php)
* **Estimated Time:** 1.0 minute
* **Screen Action:**
  1. Open **File a Claim**.
  2. Select an approved active policy from the dropdown. Note how farm details and coverage caps automatically populate in the green summary box.
  3. Input incident date, cause, damage percentage, and estimated loss amount.
  4. Upload damage incident photos and click **"Submit Claim"**.
* **🗣️ Verbal Script:**
  > *"When a disaster strikes during an active policy period, farmers file a claim directly from their portal. The system automatically binds the claim to their verified active policy, eliminating discrepancies in crop types or land parcel sizes."*

---

### Step 11: Admin & Agricultural Agent Command Center
* **Screen Display:** Switch to Window 1: [views/admin/dashboard.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/admin/dashboard.php)
* **Estimated Time:** 1.0 minute
* **Screen Action:**
  1. Highlight the high-level analytics cards: *Total Farmers, Active Policies, Pending Applications, Total Claims Filed, and Total Indemnity Paid Out*.
  2. Showcase the Chart.js visual charts displaying monthly trends and crop distributions.
* **🗣️ Verbal Script:**
  > *"Switching to the administrative view used by the Municipal Agriculture Office. The dashboard serves as an executive command center giving municipal leadership an instant snapshot of agricultural risk exposure across all barangays."*

---

### Step 12: Application Triage & 3-Tier Verification Matrix
* **Screen Display:** [views/admin/manage-applications.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/admin/manage-applications.php)
* **Estimated Time:** 1.5 minutes
* **Screen Action:**
  1. Filter applications by `Pending`.
  2. Click **"Review / Manage"** on an application to trigger the inspection modal.
  3. Demonstrate the **3-Tier Verification Matrix**:
     * 🌾 **Farm Verification** (Pending / Verified / Flagged)
     * ⚠️ **Damage Verification** (Pending / Verified / Flagged)
     * 🛡️ **Coverage Verification** (Pending / Verified / Flagged)
  4. Click **"Approve Application"** (or demonstrate entering specific rejection grounds in the rejection modal).
* **🗣️ Verbal Script:**
  > *"In the Application Management module, MAO officers adjudicate submissions using our 3-Tier Verification Matrix: separately validating the farm location, damage veracity, and coverage qualifications.
  >
  > *When an officer approves or rejects an application, the decision isn't just saved in the database—it instantly fires our dual notification pipeline."*

---

### Step 13: Claim Adjudication, Indemnity & Official Claim Slip Printing
* **Screen Display:** [views/admin/claim-verification.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/admin/claim-verification.php)
* **Estimated Time:** 1.5 minutes
* **Screen Action:**
  1. Navigate to **Claim Verification**.
  2. Open a pending claim modal. Inspect the submitted photos, estimated loss, and policy coverage limit.
  3. Enter the calculated **Indemnity Amount (₱)** and officer remarks.
  4. Click **"✅ Approve Claim"**.
  5. Click **"🖨️ Print Claim Slip"** to preview the formal indemnity approval voucher formatted for municipal treasury disbursement.
* **🗣️ Verbal Script:**
  > *"In the Claim Verification module, MAO evaluators inspect the visual damage evidence against the policy coverage ceiling. Once the approved indemnity amount is entered and approved, the system generates an official printable Claim Slip ready for submission to the Municipal Treasury for disbursement."*

---

### Step 14: Multi-Channel Notifications (PhilSMS SMS + Email Alerts)
* **Screen Display:** [views/admin/sms-logs.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/admin/sms-logs.php) & [api/helpers/notification.php](file:///C:/xampp/htdocs/web-based-crop-insurance/api/helpers/notification.php)
* **Estimated Time:** 1.0 minute
* **Screen Action:**
  1. Open the **SMS Logs** interface.
  2. Point out the KPI tiles: *Total Attempts, Sent Successfully, Failed Attempts, and Simulated Logs*.
  3. Show the real-time record of the SMS notification automatically dispatched to the farmer's mobile number:
     * Message: `LGU Sto. Nino Crop Insurance: Hello Pedro Dela Cruz, your policy POL-2026-000001 has been APPROVED...`
  4. Mention the concurrent transactional email dispatched via SMTP.
* **🗣️ Verbal Script:**
  > *"Recognizing that rural farmers in outlying barangays often lack stable Wi-Fi or mobile data, our system integrates the **PhilSMS Gateway API**.
  >
  > *The moment an application is approved or a claim indemnity is cleared, an SMS text message is transmitted directly to the farmer's mobile phone, complemented by an official email notification. The Admin SMS Log provides an auditable transmission history with delivery statuses."*

---

### Step 15: Municipal User Governance & Role Management
* **Screen Display:** [views/admin/user-management.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/admin/user-management.php)
* **Estimated Time:** 1.0 minute
* **Screen Action:**
  1. Display the user table filtered by role (*Admin, Agent, Farmer*).
  2. Show user status management (Active, Inactive, Suspended).
  3. Demonstrate creating an authorized agricultural field agent account.
* **🗣️ Verbal Script:**
  > *"User governance allows the Municipal Administrator to manage agricultural field agents, inspectors, and farmers. Administrative privileges are safeguarded so that field personnel cannot manipulate core insurance policies or system settings."*

---

### Step 16: Official LGU Agricultural Reports & Custom Signatories
* **Screen Display:** [views/admin/reports.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/admin/reports.php)
* **Estimated Time:** 1.0 minute
* **Screen Action:**
  1. Show report filters: Report Type (*Applications, Claims, All*), Status, and Date Range.
  2. Point out the **Signatories Configuration Card**: Customizing 'Prepared By' (e.g., *Municipal Agriculturist*) and 'Approved By' (e.g., *Municipal Mayor Hon. Vicente G. Pagurayan*).
  3. Scroll down to show the printable document formatted with the official header of the **Province of Cagayan, Municipality of Sto. Niño, and Municipal Agriculture Office**.
  4. Click **Print Report** (or export preview).
* **🗣️ Verbal Script:**
  > *"For LGU executive sessions, PCIC audits, and MDRRMO disaster documentation, WBCIOLSN generates formal municipal agricultural reports.
  >
  > *These reports automatically include official municipal emblems, comprehensive tabular summaries of approved policies and claims, and customizable executive signatories ready for print or PDF export."*

---

### Step 17: System Audit Trail, Conclusion & Transition to Q&A
* **Screen Display:** [views/admin/dashboard.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/admin/dashboard.php) (Audit logs section)
* **Estimated Time:** 0.5 minute
* **Screen Action:**
  1. Scroll to the audit log trail showing actor IDs, timestamps, affected entities, and system actions.
  2. Turn to the panel members for closing remarks.
* **🗣️ Verbal Script:**
  > *"Every financial change, claim adjudication, and policy approval is logged in our audit trail for municipal accountability and transparency.
  >
  > *In conclusion, WBCIOLSN transforms agricultural risk management in Sto. Niño, Cagayan from a slow, paper-bound struggle into an automated, transparent, and farmer-centered digital service.
  >
  > *Thank you very much, honorable members of the panel. We are now ready and eager to entertain your questions."*

---

## 🛡️ Capstone Defense Panelist Q&A Cheat Sheet

| Question | Recommended Technical & Local Answer |
| :--- | :--- |
| **Q1: Why build a custom web system when the Philippine Crop Insurance Corporation (PCIC) already exists?** | *"PCIC provides national insurance underwriting, but municipal-level filing, intake, and field damage validation are handled locally by the Municipal Agriculture Office (MAO). Historically, MAO staff relied on manual paper forms and physical logbooks, causing delays and lost papers. WBCIOLSN acts as the municipal digital frontliner: it automates intake, captures exact GPS coordinates, validates photographic evidence, and compiles clean, audit-ready reports that can be directly submitted to PCIC or the municipal council for disaster aid."* |
| **Q2: How can rural farmers use this if internet connectivity in far-flung barangays is poor?** | *"We adopted a dual-strategy: First, the web application is built with lightweight, mobile-responsive assets that load quickly even on 3G connections. Second, and most importantly, we integrated the **PhilSMS Gateway API** ([api/helpers/notification.php](file:///C:/xampp/htdocs/web-based-crop-insurance/api/helpers/notification.php)). Once submitted, farmers do not need continuous data to stay updated—they receive automatic SMS text alerts on their standard cellular phones whenever their application or claim progresses."* |
| **Q3: How does farm geotagging work without incurring expensive Google Maps API charges?** | *"We utilized **Leaflet.js** integrated with **OpenStreetMap (OSM)** tiles ([views/user/new-application.php](file:///C:/xampp/htdocs/web-based-crop-insurance/views/user/new-application.php)). This delivers 100% free, open-source, interactive satellite and topographic map rendering. Farmers pin their farm parcel with a click, and the system records precise decimal latitude and longitude into the database without paying recurring commercial API fees."* |
| **Q4: How does the system prevent farmers from submitting fake or repeated calamity claims?** | *"We enforce multiple defensive layers: (1) Claims can only be filed against **active, approved policies** during valid date ranges; (2) The system mandates an automated validation requiring a **minimum of 5 photo evidence uploads** of damaged crops; (3) Farm GPS coordinates must match the registered plot; and (4) The Admin portal enforces a **3-Tier Verification Matrix** (Farm, Damage, and Coverage verification) before any indemnity amount can be cleared."* |
| **Q5: How do you protect farmer data and prevent SQL injection or unauthorized access?** | *"All database queries are executed strictly through **PDO with prepared statements and parameter binding** ([api/config/database.php](file:///C:/xampp/htdocs/web-based-crop-insurance/api/config/database.php)), neutralizing SQL injection. Access is gated by JWT tokens stored securely on the client, with server-side role validation in [RoleMiddleware.php](file:///C:/xampp/htdocs/web-based-crop-insurance/api/middleware/RoleMiddleware.php). Password hashes use standard bcrypt (`PASSWORD_BCRYPT`), and inputs are sanitized to eliminate XSS."* |
| **Q6: What happens if an applicant uploads a PHP script disguised as a photo?** | *"In [api/helpers/upload.php](file:///C:/xampp/htdocs/web-based-crop-insurance/api/helpers/upload.php), the server validates the actual MIME type against an allowed whitelist (`image/jpeg`, `image/png`, `application/pdf`), limits file sizes to 5-10 MB, generates cryptographically randomized file basenames, and stores them in an `uploads/` directory where PHP script execution is strictly disabled via `.htaccess`."* |
| **Q7: Can a farmer file multiple claims for the same typhoon on one policy?** | *"The system links claims to policies and records historical indemnity totals against the policy's maximum coverage limit. Once the maximum coverage amount is exhausted, further payouts on that policy are restricted."* |

---

## 💡 Pro-Tips for Defense Day

1. **Split-Screen Dual Browser Setup:** Position the **Admin window** on the left and the **Farmer window (Incognito)** on the right. Submit a claim on the right, refresh or review on the left, and watch the status badge and PhilSMS log update instantly to visually impress the panel.
2. **Emphasize Local Impact:** Continually ground your explanations in the reality of **Sto. Niño, Cagayan** (e.g., mention palay and corn farming along the Cagayan River, typhoons, and the Municipal Agriculture Office).
3. **Showcase Printable Reports:** Show the panel the printable **Claim Slip** and **Official Crop Insurance Report** complete with the Municipal Mayor and Municipal Agriculturist signatories; panels consistently appreciate production-ready government document layouts.
4. **Have Offline Backup Data:** Ensure your local MySQL service in XAMPP has the seed data pre-loaded so your demonstration runs smoothly without relying on live external internet.
