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

function logout() {
  api("POST", "/auth/logout").catch(() => {});
  clearSession();
  showToast("Logged out", "You have been logged out successfully.", "info");
  setTimeout(() => (window.location.href = "../../index.php"), 1000);
}

function adminLogout() {
  api("POST", "/auth/logout").catch(() => {});
  clearSession();
  showToast("Logged out", "Admin session ended.", "info");
  setTimeout(() => (window.location.href = "login.php"), 1000);
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

document.addEventListener("DOMContentLoaded", () => {
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
