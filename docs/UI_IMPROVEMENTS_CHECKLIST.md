# UI Improvements Checklist

> Task-by-task fixes for the Web-Based Crop Insurance System
> Updated: 2026-06-08

---

## [ Create Account — Signup ]

- [x] **Task 1** ✅ — Phone number input: block letters, enforce PH mobile format (`09XXXXXXXXX` or `+639XXXXXXXXX`, exactly 11/13 chars). Show inline error if invalid.

---

## [ New Application — Step 2: Farm Information ]

- [x] **Task 2** ✅ — Geo Tagging: Replace the placeholder `div` with a real **Leaflet + OpenStreetMap** map. User clicks the map → marker is placed → Latitude and Longitude are auto-saved in hidden fields and submitted with the form.

---

## [ New Application — Step 3: Damage Report ]

- [x] **Task 3** ✅ — Photo Evidence: Change from single-image upload to **multi-image** (minimum 5 photos required). Show thumbnail grid preview of all selected images. Accepted: PNG, JPG, JPEG up to 10 MB each.

- [x] **Task 4** ✅ — **Percentage of Damage** auto-calculates **Financial Damage** (PH PCIC method):
  - % Damage × Total Area × crop average yield × farm-gate price
  - Auto-fill Financial Damage field when % and area are both entered
  - Add helper tooltip explaining the formula (PCIC standard)

---

## [ New Application — Step 4: Coverage & Consent ]

- [x] **Task 5** ✅ — Remove the **Signature Upload** field entirely (keep Valid ID Upload). Clean up the form-row layout to single column.

---

## [ My Applications — View Modal ]

- [x] **Task 6** ✅ — Farm Location is **missing** in the View detail modal — `p.location` is not returned by the `/policies/{id}` API. Fix: fetch farm data in `viewApp()` and display Farm Location, Area, Land Cate gory, and Tenurial Status.

---

## [ My Applications — Cancel ]

- [x] **Task 7** ✅ — Replace `confirm()` browser dialog with a styled **custom confirmation modal**. Modal shows icon, title, application ID, warning message, and Confirm / Go Back buttons. Covers both Cancel and Delete actions.

---

## Notes

- **PH Phone format**: `09XXXXXXXXX` (11 digits) or `+639XXXXXXXXX` (13 chars). No letters allowed.
- **Leaflet CDN**: `https://unpkg.com/leaflet@1.9.4/dist/leaflet.css` + `leaflet.js` — free, no API key.
- **PCIC Damage formula**: Indemnity = Coverage Amount × (% Damage ÷ 100). Financial Damage estimate = Area (ha) × Avg Yield (kg/ha) × Farm-gate Price × (% Damage ÷ 100).
- **Crop average values (PH 2024 reference)**:
  | Crop | Avg Yield | Avg Farm-gate Price | Value/ha |
  |---|---|---|---|
  | Rice | 70 bags/ha (50 kg ea) | ₱1,200/bag | ₱84,000/ha |
  | Corn | 3,500 kg/ha | ₱14/kg | ₱49,000/ha |
  | Sugarcane | 65,000 kg/ha | ₱2.5/kg | ₱162,500/ha |
  | Banana | 20,000 kg/ha | ₱15/kg | ₱300,000/ha |
  | Coconut | 5,000 nuts/ha | ₱8/nut | ₱40,000/ha |
  | Vegetables | 10,000 kg/ha | ₱25/kg | ₱250,000/ha |
  | Cassava | 20,000 kg/ha | ₱4/kg | ₱80,000/ha |
  | Coffee | 500 kg/ha | ₱200/kg | ₱100,000/ha |
  | Mango | 8,000 kg/ha | ₱35/kg | ₱280,000/ha |
  | Tobacco | 1,200 kg/ha | ₱80/kg | ₱96,000/ha |
