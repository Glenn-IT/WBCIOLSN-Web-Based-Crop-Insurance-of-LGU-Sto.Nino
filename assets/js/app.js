// ============================================================
// app.js — Global utilities, toast, auth guards, sidebar
// ============================================================

// ── API Base ──────────────────────────────────────────────
const API_BASE = "/web-based-crop-insurance/api";

/**
 * Fetch wrapper for the backend API.
 * Returns parsed JSON { success, data, message } or throws on network error.
 */
async function api(method, endpoint, body = null, requiresAuth = true) {
  const headers = { "Content-Type": "application/json" };
  if (requiresAuth) {
    const token = getToken();
    if (token) headers["Authorization"] = "Bearer " + token;
  }
  const opts = { method, headers };
  if (body && method !== "GET") opts.body = JSON.stringify(body);

  const res = await fetch(API_BASE + endpoint, opts);
  const json = await res
    .json()
    .catch(() => ({ success: false, message: "Invalid server response." }));
  return { status: res.status, ...json };
}

// ── Token / Session helpers ───────────────────────────────
function getToken() {
  return localStorage.getItem("lgu_token") || null;
}

function setSession(user, token) {
  localStorage.setItem("lgu_token", token);
  localStorage.setItem("lgu_current_user", JSON.stringify(user));
  // Mirror token in a cookie so PHP auth-guard.php can verify it server-side
  const maxAge = 86400; // 24 h — matches JWT_EXPIRY
  document.cookie = `lgu_token=${token};path=/;max-age=${maxAge};SameSite=Lax`;
}

function clearSession() {
  localStorage.removeItem("lgu_token");
  localStorage.removeItem("lgu_current_user");
  localStorage.removeItem("lgu_admin_logged_in");
  // Expire the auth cookie
  document.cookie = "lgu_token=;path=/;max-age=0;SameSite=Lax";
}

function getCurrentUser() {
  try {
    return JSON.parse(localStorage.getItem("lgu_current_user") || "null");
  } catch {
    return null;
  }
}

function isAdminLoggedIn() {
  const user = getCurrentUser();
  return user && (user.role === "admin" || user.role === "agent");
}

// ── Toast Notification System ─────────────────────────────
function showToast(title, message, type = "success") {
  // Allow 2-arg call: showToast("message", "type") — treat title as message
  if (
    type === "success" &&
    (message === "success" ||
      message === "error" ||
      message === "warning" ||
      message === "info")
  ) {
    type = message;
    message = title;
    title = type.charAt(0).toUpperCase() + type.slice(1);
  } else if (message === undefined) {
    message = title;
    title = type.charAt(0).toUpperCase() + type.slice(1);
  }
  const container = document.getElementById("toast-container");
  if (!container) return;

  const colors = {
    success: "#28a745",
    error: "#dc3545",
    warning: "#ffc107",
    info: "#17a2b8",
  };

  const icons = {
    success: "✅",
    error: "❌",
    warning: "⚠️",
    info: "ℹ️",
  };

  const toast = document.createElement("div");
  toast.className = "toast";
  toast.style.setProperty("--toast-color", colors[type]);
  toast.innerHTML = `
    <span class="toast-icon">${icons[type]}</span>
    <div class="toast-content">
      <h6>${title}</h6>
      <p>${message}</p>
    </div>
    <button class="toast-close" onclick="removeToast(this.parentElement)">×</button>
    <div class="toast-progress"></div>
  `;

  container.appendChild(toast);

  setTimeout(() => removeToast(toast), 3200);
}

function removeToast(toast) {
  if (!toast || toast.classList.contains("removing")) return;
  toast.classList.add("removing");
  setTimeout(() => toast.remove(), 300);
}

// ── Loading Spinner ────────────────────────────────────────
function showLoading() {
  const overlay = document.createElement("div");
  overlay.className = "loading-overlay";
  overlay.id = "loading-overlay";
  overlay.innerHTML = `<div class="spinner"></div>`;
  document.body.appendChild(overlay);
}

function hideLoading() {
  const overlay = document.getElementById("loading-overlay");
  if (overlay) overlay.remove();
}

function simulateLoading(callback, ms = 800) {
  showLoading();
  setTimeout(() => {
    hideLoading();
    if (callback) callback();
  }, ms);
}

// ── Modal Helpers ──────────────────────────────────────────
function openModal(id) {
  const modal = document.getElementById(id);
  if (modal) {
    modal.classList.add("active");
    document.body.style.overflow = "hidden";
  }
}

function closeModal(id) {
  const modal = document.getElementById(id);
  if (modal) {
    modal.classList.remove("active");
    document.body.style.overflow = "";
  }
}

// Close modal on overlay click
document.addEventListener("click", (e) => {
  if (e.target.classList.contains("modal-overlay")) {
    e.target.classList.remove("active");
    document.body.style.overflow = "";
  }
});

// Close on Escape
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") {
    document.querySelectorAll(".modal-overlay.active").forEach((m) => {
      m.classList.remove("active");
      document.body.style.overflow = "";
    });
  }
});

// ── Auth Guard ────────────────────────────────────────────
function requireAuth() {
  const user = getCurrentUser();
  const token = getToken();
  if (!user || !token) {
    window.location.href = "../../index.php";
    return null;
  }
  return user;
}

function requireAdmin() {
  const user = getCurrentUser();
  const token = getToken();
  if (!user || !token || (user.role !== "admin" && user.role !== "agent")) {
    window.location.href = "login.php";
    return null;
  }
  return user;
}

// ── Logout & Confirmation ─────────────────────────────────
let pendingLogoutType = "user";

function showLogoutConfirmModal(isAdmin = false) {
  pendingLogoutType = isAdmin ? "admin" : "user";

  if (!document.getElementById("logout-confirm-modal")) {
    injectLogoutModal();
  }

  const titleEl = document.getElementById("logout-modal-title");
  const msgEl = document.getElementById("logout-modal-message");
  const subEl = document.getElementById("logout-modal-subtext");
  const confirmBtn = document.getElementById("logout-confirm-btn");

  if (isAdmin) {
    if (titleEl) titleEl.textContent = "Admin Sign Out";
    if (msgEl) msgEl.textContent = "Are you sure you want to end your administrative session?";
    if (subEl) subEl.textContent = "You will need to sign in again to access the admin portal.";
  } else {
    if (titleEl) titleEl.textContent = "Sign Out";
    if (msgEl) msgEl.textContent = "Are you sure you want to sign out?";
    if (subEl) subEl.textContent = "You will need to sign in again to access your account and applications.";
  }

  if (confirmBtn) {
    confirmBtn.disabled = false;
    confirmBtn.innerHTML = "<span>🚪</span> Yes, Sign Out";
  }

  openModal("logout-confirm-modal");
}

function executeLogout() {
  const confirmBtn = document.getElementById("logout-confirm-btn");
  if (confirmBtn) {
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = "<span>⏳</span> Signing out...";
  }

  if (pendingLogoutType === "admin") {
    adminLogout(true);
  } else {
    logout(true);
  }
}

function confirmLogout() {
  showLogoutConfirmModal(false);
}

function confirmAdminLogout() {
  showLogoutConfirmModal(true);
}

function logout(confirmed = false) {
  if (!confirmed) {
    showLogoutConfirmModal(false);
    return;
  }
  closeModal("logout-confirm-modal");
  api("POST", "/auth/logout").catch(() => {});
  clearSession();
  showToast("Logged out", "You have been logged out successfully.", "info");
  setTimeout(() => window.location.replace("../../index.php"), 1000);
}

function adminLogout(confirmed = false) {
  if (!confirmed) {
    showLogoutConfirmModal(true);
    return;
  }
  closeModal("logout-confirm-modal");
  api("POST", "/auth/logout").catch(() => {});
  clearSession();
  showToast("Logged out", "Admin session ended.", "info");
  setTimeout(() => window.location.replace("login.php"), 1000);
}

function injectLogoutModal() {
  if (document.getElementById("logout-confirm-modal")) return;

  const modalHtml = `
  <div class="modal-overlay" id="logout-confirm-modal" style="z-index: 9999;">
    <div class="modal" style="max-width: 420px; width: 100%; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
      <div class="modal-header" style="background: #fff8f6; border-bottom: 1px solid #fee2e2; padding: 18px 22px;">
        <h4 style="color: #b91c1c; margin: 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
          <span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;background:#fee2e2;border-radius:50%;font-size:14px;">🚪</span>
          <span id="logout-modal-title">Sign Out</span>
        </h4>
        <button type="button" class="modal-close" onclick="closeModal('logout-confirm-modal')" style="color: #b91c1c;" aria-label="Close">×</button>
      </div>
      <div class="modal-body" style="padding: 22px;">
        <p id="logout-modal-message" style="margin: 0 0 8px; font-size: 14.5px; line-height: 1.5; color: var(--text-primary); font-weight: 500;">
          Are you sure you want to sign out?
        </p>
        <p id="logout-modal-subtext" style="margin: 0; font-size: 13px; color: var(--text-muted); line-height: 1.4;">
          You will need to sign in again to access your account.
        </p>
      </div>
      <div class="modal-footer" style="padding: 16px 22px; background: #fafafa; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" class="btn btn-ghost" onclick="closeModal('logout-confirm-modal')" style="padding: 9px 18px; font-size: 13.5px;">
          Cancel
        </button>
        <button type="button" class="btn btn-danger" id="logout-confirm-btn" onclick="executeLogout()" style="padding: 9px 20px; font-size: 13.5px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
          <span>🚪</span> Yes, Sign Out
        </button>
      </div>
    </div>
  </div>`;
  document.body.insertAdjacentHTML("beforeend", modalHtml);
}


// ── Sidebar Navigation ────────────────────────────────────
function setActiveNav(href) {
  document.querySelectorAll(".nav-item").forEach((item) => {
    item.classList.remove("active");
    if (item.dataset.href && item.dataset.href.includes(href)) {
      item.classList.add("active");
    }
  });
}

function navigateTo(path) {
  simulateLoading(() => {
    window.location.href = path;
  }, 400);
}

// ── Password Toggle ────────────────────────────────────────
function togglePassword(inputId, iconEl) {
  const input = document.getElementById(inputId);
  if (!input) return;
  if (input.type === "password") {
    input.type = "text";
    iconEl.textContent = "🙈";
  } else {
    input.type = "password";
    iconEl.textContent = "👁️";
  }
}

// ── Debounce ──────────────────────────────────────────────
function debounce(fn, delay = 300) {
  let timer;
  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => fn(...args), delay);
  };
}

// ── Format Helpers (re-exported for pages that load app.js) ──
if (typeof formatCurrency === "undefined") {
  window.formatCurrency = function (amount) {
    return new Intl.NumberFormat("en-PH", {
      style: "currency",
      currency: "PHP",
    }).format(amount);
  };
}

if (typeof formatDate === "undefined") {
  window.formatDate = function (dateString) {
    if (!dateString) return "—";
    const d = new Date(dateString);
    return d.toLocaleDateString("en-PH", {
      year: "numeric",
      month: "long",
      day: "numeric",
    });
  };
}

// ── Number Counter Animation ──────────────────────────────
function animateCounter(el, target, duration = 1200) {
  const start = 0;
  const startTime = performance.now();
  const isFloat = target % 1 !== 0;

  function update(currentTime) {
    const elapsed = currentTime - startTime;
    const progress = Math.min(elapsed / duration, 1);
    const eased = 1 - Math.pow(1 - progress, 3);
    const current = start + (target - start) * eased;
    el.textContent = isFloat
      ? current.toFixed(1)
      : Math.round(current).toLocaleString();
    if (progress < 1) requestAnimationFrame(update);
  }

  requestAnimationFrame(update);
}

// ── Initialize Topbar User Info ───────────────────────────
async function initTopbarUser() {
  let user = getCurrentUser();
  const el = document.getElementById("topbar-user-name");
  const avatarEl = document.getElementById("topbar-avatar");
  const sidebarName = document.getElementById("sidebar-name");
  const sidebarAvatar = document.getElementById("sidebar-avatar");

  // Fallback: If user data is missing in localStorage, load fresh from API
  if ((!user || !user.first_name) && getToken()) {
    try {
      const res = await api("GET", "/auth/me");
      if (res && res.success && res.data) {
        user = res.data;
        localStorage.setItem("lgu_current_user", JSON.stringify(user));
      }
    } catch (e) {}
  }

  if (user) {
    const first = user.first_name || user.firstName || "";
    const last = user.last_name || user.lastName || "";
    const fullName = `${first} ${last}`.trim() || user.email || (user.role === "admin" ? "Administrator" : "Farmer");
    const initials = (
      (first.charAt(0) || (user.role === "admin" ? "A" : "F")) +
      (last.charAt(0) || "")
    ).toUpperCase();

    if (el && (!el.textContent || el.textContent.trim() === "Loading...")) {
      el.textContent = fullName;
    } else if (el) {
      el.textContent = fullName;
    }
    if (avatarEl) avatarEl.textContent = initials;
    if (sidebarName) sidebarName.textContent = fullName;
    if (sidebarAvatar) sidebarAvatar.textContent = initials;
  }
}

function initAdminTopbar() {
  initTopbarUser();
}

// ── Notifications ─────────────────────────────────────────
async function initNotifications() {
  if (!getToken()) return;
  try {
    const res = await api("GET", "/notifications/unread-count");
    if (res.success) updateNotifBadge(res.data.unread ?? 0);
  } catch (e) {}
  // Close dropdown when clicking outside
  document.addEventListener("click", (e) => {
    const dropdown = document.getElementById("notif-dropdown");
    const btn = document.getElementById("notif-btn");
    if (
      dropdown &&
      dropdown.style.display === "block" &&
      !dropdown.contains(e.target) &&
      btn &&
      !btn.contains(e.target)
    ) {
      dropdown.style.display = "none";
    }
  });
}

function updateNotifBadge(count) {
  const dot = document.getElementById("notif-dot");
  const badge = document.getElementById("notif-count");
  if (dot) dot.style.display = count > 0 ? "inline-block" : "none";
  if (badge) {
    badge.style.display = count > 0 ? "inline" : "none";
    badge.textContent = count > 99 ? "99+" : count;
  }
}

async function toggleNotifDropdown() {
  const dropdown = document.getElementById("notif-dropdown");
  if (!dropdown) return;
  if (dropdown.style.display === "block") {
    dropdown.style.display = "none";
    return;
  }
  dropdown.style.display = "block";
  await loadNotifications();
}

async function loadNotifications() {
  const list = document.getElementById("notif-list");
  if (list)
    list.innerHTML =
      '<div style="padding:20px;text-align:center;color:var(--text-muted);font-size:13px">Loading…</div>';
  try {
    const res = await api("GET", "/notifications?per_page=10");
    if (!res.success) {
      if (list)
        list.innerHTML =
          '<div style="padding:20px;text-align:center;color:var(--text-muted);font-size:13px">Failed to load.</div>';
      return;
    }
    const items = res.data || [];
    updateNotifBadge(items.filter((n) => !parseInt(n.is_read)).length);
    if (!items.length) {
      if (list)
        list.innerHTML =
          '<div style="padding:28px;text-align:center;color:var(--text-muted);font-size:13px">🔔 No notifications yet.</div>';
      return;
    }
    if (list) {
      list.innerHTML = items
        .map((n) => {
          const unread = !parseInt(n.is_read);
          const bg = unread ? "#f0f4ff" : "#fff";
          return `<div
            data-notif-id="${n.id}"
            onclick="markNotifRead(${n.id}, this, '${(n.link || "").replace(/'/g, "\\'")}')"
            style="padding:12px 14px;border-bottom:1px solid var(--border-color);
              cursor:pointer;background:${bg};transition:background 0.15s"
            onmouseover="this.style.background='#f8fafc'"
            onmouseout="this.style.background='${bg}'">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px">
              <div style="display:flex;align-items:center;gap:6px">
                ${unread ? '<span style="width:8px;height:8px;border-radius:50%;background:#1a73e8;flex-shrink:0;display:inline-block"></span>' : ""}
                <strong style="font-size:13px;line-height:1.3">${n.title}</strong>
              </div>
              <span style="font-size:11px;color:var(--text-muted);white-space:nowrap;flex-shrink:0">${timeAgo(n.created_at)}</span>
            </div>
            <p style="font-size:12.5px;color:var(--text-secondary);margin:4px 0 0 ${unread ? "14px" : "0"}">${n.message}</p>
          </div>`;
        })
        .join("");
    }
  } catch (e) {
    if (list)
      list.innerHTML =
        '<div style="padding:20px;text-align:center;color:var(--text-muted);font-size:13px">Error loading notifications.</div>';
  }
}

async function markNotifRead(id, el, link) {
  try {
    await api("PUT", `/notifications/${id}/read`);
    if (el) {
      el.style.background = "#fff";
      el.onmouseout = () => (el.style.background = "#fff");
      const dot = el.querySelector('span[style*="border-radius:50%"]');
      if (dot) dot.remove();
    }
    const countRes = await api("GET", "/notifications/unread-count");
    if (countRes.success) updateNotifBadge(countRes.data.unread ?? 0);
  } catch (e) {}
  if (link) window.location.href = link;
}

async function markAllNotifsRead() {
  try {
    await api("PUT", "/notifications/read-all");
    updateNotifBadge(0);
    await loadNotifications();
  } catch (e) {}
}

async function clearNotifications() {
  try {
    await api("DELETE", "/notifications/clear");
    await loadNotifications();
  } catch (e) {}
}

function timeAgo(dateString) {
  if (!dateString) return "";
  const diff = Math.floor((Date.now() - new Date(dateString)) / 1000);
  if (diff < 60) return "Just now";
  if (diff < 3600) return Math.floor(diff / 60) + "m ago";
  if (diff < 86400) return Math.floor(diff / 3600) + "h ago";
  return Math.floor(diff / 86400) + "d ago";
}

// Guard against bfcache restoring a protected page after logout.
// When the browser shows a page from its back-forward cache (persisted = true),
// re-check the session and redirect to login if the token is gone.
window.addEventListener("pageshow", function (e) {
  if (!e.persisted) return;
  const token = localStorage.getItem("lgu_token");
  if (!token) {
    const isAdmin = window.location.pathname.includes("/admin/");
    window.location.replace(
      isAdmin
        ? "/web-based-crop-insurance/views/admin/login.php"
        : "/web-based-crop-insurance/index.php"
    );
  }
});

document.addEventListener("DOMContentLoaded", () => {
  // Auto-init user profile in topbar & sidebar
  if (document.getElementById("topbar-user-name") || document.getElementById("sidebar-name")) {
    initTopbarUser();
  }
  // Auto-init counters
  document.querySelectorAll("[data-counter]").forEach((el) => {
    const val = parseFloat(el.dataset.counter);
    animateCounter(el, val);
  });
  // Auto-init notifications on all pages that have the topbar bell
  if (document.getElementById("notif-btn")) initNotifications();
});

function getStatusBadge(status) {
  const map = {
    active: "badge bg-success",
    approved: "badge bg-success",
    verified: "badge bg-success",
    paid: "badge bg-info",
    pending: "badge bg-warning text-dark",
    submitted: "badge bg-warning text-dark",
    under_review: "badge bg-primary",
    rejected: "badge bg-danger",
    cancelled: "badge bg-danger",
    inactive: "badge bg-secondary",
  };
  const labelMap = {
    active: "Active",
    approved: "Approved",
    verified: "Verified",
    paid: "Paid",
    pending: "Pending",
    submitted: "Submitted",
    under_review: "Under Review",
    rejected: "Rejected",
    cancelled: "Cancelled",
    inactive: "Inactive",
  };
  const s = (status || "").toLowerCase().replace(/ /g, "_");
  const cls = map[s] || "badge bg-secondary";
  const label = labelMap[s] || status || "—";
  return '<span class="' + cls + '">' + label + "</span>";
}

/**
 * Universal Official Government Document Printer
 * Prints any modal or content block with LGU Sto. Niño letterhead,
 * signatories, and official government footer.
 */
function printGovernmentDocument({
  title = "Official Document",
  subtitle = "Municipal Agriculture Office • LGU Sto. Niño, Cagayan",
  contentHtml = "",
  docType = "Official Record",
  docRef = "",
  signatories = null
}) {
  const now = new Date();
  const pad = (n) => String(n).padStart(2, "0");
  const generatedDate = now.toLocaleString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit"
  });
  const refCode = docRef || `WBCI-DOC-${now.getFullYear()}${pad(now.getMonth() + 1)}${pad(now.getDate())}-${pad(now.getHours())}${pad(now.getMinutes())}`;
  const currentUser = getCurrentUser();
  const userText = currentUser
    ? `${currentUser.first_name || ""} ${currentUser.last_name || ""}`.trim()
    : "Authorized Personnel";

  const origin = window.location.origin;
  const logo1 = origin + "/web-based-crop-insurance/img/Agri-Sto-Logo.png";
  const logo2 = origin + "/web-based-crop-insurance/img/Municipality-logo.png";
  const cssPath = origin + "/web-based-crop-insurance/assets/css/style.css";

  let sigHtml = "";
  if (signatories !== false) {
    const prepName = (signatories && signatories.preparedByName) || userText || "Department Staff";
    const prepPos  = (signatories && signatories.preparedByPos)  || "Municipal Agriculturist / Staff";
    const appName  = (signatories && signatories.approvedByName) || "Hon. Vicente G. Pagurayan";
    const appPos   = (signatories && signatories.approvedByPos)  || "Municipal Mayor";

    sigHtml = `
      <div style="display:flex;justify-content:space-between;margin-top:35px;padding-top:15px;page-break-inside:avoid">
        <div style="flex:1;max-width:280px">
          <p style="font-size:11px;color:#000;margin-bottom:32px">Prepared / Verified by:</p>
          <div style="font-size:12px;font-weight:700;color:#000;text-transform:uppercase;border-bottom:1px solid #000;padding-bottom:2px;min-width:200px;display:inline-block">${prepName}</div>
          <div style="font-size:11px;color:#333;margin-top:3px">${prepPos}</div>
        </div>
        <div style="flex:1;max-width:280px;text-align:right">
          <p style="font-size:11px;color:#000;margin-bottom:32px;text-align:left;display:inline-block;min-width:200px">Approved by:</p><br/>
          <div style="font-size:12px;font-weight:700;color:#000;text-transform:uppercase;border-bottom:1px solid #000;padding-bottom:2px;min-width:200px;display:inline-block;text-align:left">${appName}</div>
          <div style="font-size:11px;color:#333;margin-top:3px;min-width:200px;text-align:left;display:inline-block">${appPos}</div>
        </div>
      </div>
    `;
  }

  const printHtml = `<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>${title} — LGU Sto. Niño</title>
  <link rel="stylesheet" href="${cssPath}" />
  <style>
    @page { size: auto; margin: 12mm 15mm 12mm 15mm; }
    body { background: #fff !important; color: #000 !important; font-size: 11.5px; padding: 15px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.4; }
    .print-sheet { max-width: 860px; margin: 0 auto; background: #fff; }
    .official-report-header { display: flex; align-items: center; justify-content: space-between; padding-bottom: 8px; gap: 20px; }
    .official-logo { width: 75px; height: 75px; object-fit: contain; }
    .official-text { text-align: center; flex-grow: 1; line-height: 1.35; }
    .official-text .line { font-size: 13.5px; font-weight: 700; color: #111; margin: 0; }
    .official-text .report-title { font-size: 16px; font-weight: 800; color: #111; margin-top: 8px; text-transform: uppercase; }
    .official-divider { border: 0; border-top: 2px solid #222; margin: 10px 0 14px 0; }
    .report-meta { display: flex; justify-content: space-between; font-size: 11px; color: #444; margin-bottom: 16px; padding-bottom: 6px; border-bottom: 1px dashed #ccc; }
    .detail-section { margin-bottom: 16px; page-break-inside: avoid; }
    .detail-section-title { font-size: 12px; font-weight: 700; text-transform: uppercase; color: #1b5e20; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 8px; }
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .detail-item label { font-size: 10px; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 2px; }
    .detail-item p { margin: 0; font-size: 12px; font-weight: 600; color: #111; }
    table { width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 10px; }
    th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
    th { background: #eee !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; font-weight: 700; }
  </style>
</head>
<body>
  <div class="print-sheet">
    <div class="official-report-header">
      <img src="${logo1}" alt="MAO Logo" class="official-logo" />
      <div class="official-text">
        <div class="line">Republic of the Philippines</div>
        <div class="line">Province of Cagayan</div>
        <div class="line">Municipality of Sto. Niño</div>
        <div class="report-title">${title}</div>
        ${subtitle ? `<div style="font-size:11.5px;color:#475569;margin-top:2px">${subtitle}</div>` : ""}
      </div>
      <img src="${logo2}" alt="Municipality Logo" class="official-logo" />
    </div>
    <hr class="official-divider" />
    <div class="report-meta">
      <span><strong>Date Generated:</strong> ${generatedDate}</span>
      <span><strong>Printed By:</strong> ${userText}</span>
      <span><strong>Doc Ref:</strong> ${refCode}</span>
    </div>

    <div class="print-content-body">
      ${contentHtml}
    </div>

    ${sigHtml}

    <!-- Official Government Footer -->
    <div class="official-gov-footer" style="display:block;margin-top:28px;padding-top:10px;page-break-inside:avoid">
      <div class="gov-footer-divider" style="border-top:2px solid #000;margin-bottom:8px"></div>
      <div class="gov-footer-body" style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:nowrap">
        <div class="gov-footer-brand" style="display:flex;align-items:center;gap:10px;flex:1">
          <img src="${logo1}" alt="Seal" class="gov-footer-seal" style="width:38px;height:38px;object-fit:contain" />
          <div class="gov-footer-brand-text">
            <div class="gov-office-title" style="font-size:11.5px;font-weight:800;color:#000">MUNICIPAL AGRICULTURE OFFICE</div>
            <div class="gov-office-sub" style="font-size:9.5px;color:#333">Local Government Unit of Sto. Niño • Province of Cagayan</div>
            <div class="gov-office-addr" style="font-size:9px;color:#555">📍 Municipal Hall Compound, Centro Norte, Sto. Niño (Faire), Cagayan 3525</div>
          </div>
        </div>
        <div class="gov-footer-info-grid" style="display:flex;gap:20px;font-size:9.5px;color:#222">
          <div>
            <div style="font-weight:700;font-size:9.5px;border-bottom:1px solid #333;margin-bottom:2px">Official Contact</div>
            <div>Email: agriculture@stonino-cagayan.gov.ph</div>
            <div>Hotline: (078) 377-2001 / +63 917 123 4567</div>
          </div>
          <div>
            <div style="font-weight:700;font-size:9.5px;border-bottom:1px solid #333;margin-bottom:2px">Document Control</div>
            <div>System: WBCI-OLSN v1.0</div>
            <div>Doc Ref: <strong>${refCode}</strong></div>
          </div>
        </div>
      </div>
      <div class="gov-footer-notice" style="background:#fff;border:1px solid #333;padding:5px 8px;margin-top:8px;display:flex;justify-content:space-between;align-items:center;font-size:9px">
        <div style="color:#222;font-style:italic">
          ⚖️ Official System-Generated Document — LGU Sto. Niño, Cagayan. Valid only with authorized signature(s) and municipal seal.
        </div>
        <div style="font-weight:700;color:#000;text-transform:uppercase">
          ★ Republika ng Pilipinas ★
        </div>
      </div>
    </div>
  </div>
  <script>
    window.onload = function() {
      setTimeout(function() {
        window.print();
      }, 250);
    };
  <\/script>
</body>
</html>`;

  const printWin = window.open("", "_blank", "width=920,height=750");
  if (printWin) {
    printWin.document.open();
    printWin.document.write(printHtml);
    printWin.document.close();
  } else {
    alert("Please allow popups to open the official printable document.");
  }
}

