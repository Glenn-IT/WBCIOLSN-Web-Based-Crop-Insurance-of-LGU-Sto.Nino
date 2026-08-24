<?php
// Variables the calling page may set before including this file:
//   $topbarTitle    (string) — bold page title shown in topbar
//   $topbarSubtitle (string) — smaller subtitle; use '' to show live date via JS
//   $isAdmin        (bool)   — true for admin pages; changes avatar style & profile link
$topbarTitle    = $topbarTitle    ?? 'Page';
$topbarSubtitle = $topbarSubtitle ?? '';
$isAdmin        = $isAdmin        ?? false;

$profileHref   = $isAdmin ? 'admin-profile.php' : 'profile.php';
$avatarStyle   = $isAdmin
    ? 'width:30px;height:30px;font-size:12px;background:linear-gradient(135deg,#1a237e,#283593);'
    : 'width:30px;height:30px;font-size:12px;';

$tbFirst = $authUser['first_name'] ?? '';
$tbLast  = $authUser['last_name']  ?? '';
$tbFull  = trim("$tbFirst $tbLast");
if (!$tbFull) {
    $tbFull = $authUser['email'] ?? ($isAdmin ? 'Administrator' : 'Farmer');
}
$tbInitials = strtoupper(
    substr($tbFirst ?: ($isAdmin ? 'A' : 'F'), 0, 1) .
    substr($tbLast ?: '', 0, 1)
);
?>
<header class="topbar">
  <div class="topbar-left">
    <div>
      <div class="topbar-title"><?= htmlspecialchars($topbarTitle) ?></div>
      <div class="topbar-subtitle" id="topbar-subtitle">
        <?= htmlspecialchars($topbarSubtitle) ?>
      </div>
    </div>
  </div>
  <div class="topbar-right">
    <!-- Notification bell -->
    <div style="position:relative">
      <div class="topbar-btn" id="notif-btn" title="Notifications"
        onclick="toggleNotifDropdown()" style="cursor:pointer;user-select:none">
        🔔<span class="notification-dot" id="notif-dot" style="display:none"></span>
        <span id="notif-count"
          style="display:none;background:#dc3545;color:#fff;border-radius:10px;
            font-size:10px;font-weight:700;padding:1px 5px;margin-left:2px;
            vertical-align:middle"></span>
      </div>

      <!-- Notification dropdown -->
      <div id="notif-dropdown"
        style="display:none;position:absolute;right:0;top:calc(100% + 8px);
          width:340px;background:#fff;border:1px solid var(--border-color);
          border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,0.15);
          z-index:1000;overflow:hidden">
        <div style="display:flex;justify-content:space-between;align-items:center;
          padding:12px 14px;border-bottom:1px solid var(--border-color);background:#f8fafc">
          <strong style="font-size:14px">🔔 Notifications</strong>
          <div style="display:flex;gap:10px">
            <button onclick="markAllNotifsRead()"
              style="font-size:11.5px;color:var(--primary);background:none;border:none;
                cursor:pointer;font-weight:600;padding:0">Mark all read</button>
            <button onclick="clearNotifications()"
              style="font-size:11.5px;color:var(--text-muted);background:none;border:none;
                cursor:pointer;padding:0">Clear all</button>
          </div>
        </div>
        <div id="notif-list" style="max-height:320px;overflow-y:auto"></div>
      </div>
    </div>

    <!-- User chip -->
    <div
      style="
        display:flex;align-items:center;gap:10px;
        padding:6px 14px;background:#f8fafc;
        border-radius:30px;border:1px solid var(--border-color);cursor:pointer;
      "
      onclick="navigateTo('<?= $profileHref ?>')"
    >
      <div class="user-avatar" id="topbar-avatar" style="<?= $avatarStyle ?>"><?= htmlspecialchars($tbInitials) ?></div>
      <span id="topbar-user-name" style="font-size:13px;font-weight:600;color:var(--text-primary);">
        <?= htmlspecialchars($tbFull) ?>
      </span>
    </div>
  </div>
</header>
