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
}

function clearSession() {
  localStorage.removeItem("lgu_token");
  localStorage.removeItem("lgu_current_user");
  localStorage.removeItem("lgu_admin_logged_in");
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
    window.location.href = "../../index.html";
    return null;
  }
  return user;
}

function requireAdmin() {
  const user = getCurrentUser();
  const token = getToken();
  if (!user || !token || (user.role !== "admin" && user.role !== "agent")) {
    window.location.href = "login.html";
    return null;
  }
  return user;
}

function logout() {
  api("POST", "/auth/logout").catch(() => {});
  clearSession();
  showToast("Logged out", "You have been logged out successfully.", "info");
  setTimeout(() => (window.location.href = "../../index.html"), 1000);
}

function adminLogout() {
  api("POST", "/auth/logout").catch(() => {});
  clearSession();
  showToast("Logged out", "Admin session ended.", "info");
  setTimeout(() => (window.location.href = "login.html"), 1000);
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
function initTopbarUser() {
  const user = getCurrentUser();
  const el = document.getElementById("topbar-user-name");
  const avatarEl = document.getElementById("topbar-avatar");
  const sidebarName = document.getElementById("sidebar-name");
  const sidebarAvatar = document.getElementById("sidebar-avatar");

  if (user) {
    const first = user.first_name || user.firstName || "";
    const last = user.last_name || user.lastName || "";
    const fullName = `${first} ${last}`.trim() || user.email || "User";
    const initials = (first.charAt(0) + last.charAt(0)).toUpperCase() || "U";

    if (el) el.textContent = fullName;
    if (avatarEl) avatarEl.textContent = initials;
    if (sidebarName) sidebarName.textContent = fullName;
    if (sidebarAvatar) sidebarAvatar.textContent = initials;
  }
}

function initAdminTopbar() {
  const user = getCurrentUser();
  const el = document.getElementById("topbar-user-name");
  const avatarEl = document.getElementById("topbar-avatar");
  const sidebarName = document.getElementById("sidebar-name");
  const sidebarAvatar = document.getElementById("sidebar-avatar");

  if (user) {
    const first = user.first_name || user.firstName || "";
    const last = user.last_name || user.lastName || "";
    const fullName = `${first} ${last}`.trim() || user.email || "Admin";
    const initials = (first.charAt(0) + last.charAt(0)).toUpperCase() || "AL";

    if (el) el.textContent = fullName;
    if (avatarEl) avatarEl.textContent = initials;
    if (sidebarName) sidebarName.textContent = fullName;
    if (sidebarAvatar) sidebarAvatar.textContent = initials;
  } else {
    if (el) el.textContent = "Admin LGU";
    if (avatarEl) avatarEl.textContent = "AL";
    if (sidebarName) sidebarName.textContent = "Admin LGU";
    if (sidebarAvatar) sidebarAvatar.textContent = "AL";
  }
}

document.addEventListener("DOMContentLoaded", () => {
  // Auto-init counters
  document.querySelectorAll("[data-counter]").forEach((el) => {
    const val = parseFloat(el.dataset.counter);
    animateCounter(el, val);
  });
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
