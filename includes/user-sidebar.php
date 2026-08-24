<?php
// Set $currentPage before including to highlight the active nav item.
// Accepted values: 'dashboard', 'new-application', 'my-applications',
//                  'application-status', 'file-claim', 'profile', 'about'
$currentPage = $currentPage ?? '';

function userNavItem(string $page, string $icon, string $label, string $current): string {
    $active = ($current === $page) ? ' active' : '';
    return '<div class="nav-item' . $active . '" onclick="navigateTo(\'' . $page . '.php\')">'
         . '<span class="nav-icon">' . $icon . '</span>' . htmlspecialchars($label)
         . '</div>';
}
?>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <div class="sidebar-logo">🌽</div>
    <div>
      <div class="sidebar-title">LGU Sto. Niño</div>
      <div class="sidebar-subtitle">Crop Insurance System</div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section-label">Main</div>
    <?= userNavItem('dashboard', '📊', 'Dashboard', $currentPage) ?>

    <div class="nav-section-label">Applications</div>
    <?= userNavItem('new-application', '📝', 'New Application', $currentPage) ?>
    <?= userNavItem('my-applications', '📁', 'My Applications', $currentPage) ?>
    <?= userNavItem('application-status', '🔍', 'Application Status', $currentPage) ?>

    <div class="nav-section-label">Claims</div>
    <?= userNavItem('file-claim', '📩', 'File a Claim', $currentPage) ?>

    <div class="nav-section-label">Account & Info</div>
    <?= userNavItem('profile', '👤', 'My Profile', $currentPage) ?>
    <?= userNavItem('about',   'ℹ️', 'About System', $currentPage) ?>
  </nav>

<?php
$sbFirst = $authUser['first_name'] ?? '';
$sbLast  = $authUser['last_name']  ?? '';
$sbFull  = trim("$sbFirst $sbLast");
if (!$sbFull) {
    $sbFull = $authUser['email'] ?? 'Farmer';
}
$sbInitials = strtoupper(
    substr($sbFirst ?: 'F', 0, 1) .
    substr($sbLast ?: '', 0, 1)
);
$sbRole = !empty($authUser['farmer_type']) ? ucfirst($authUser['farmer_type']) : 'Farmer';
?>
  <div class="sidebar-footer">
    <div class="user-info-sidebar" onclick="navigateTo('profile.php')">
      <div class="user-avatar" id="sidebar-avatar"><?= htmlspecialchars($sbInitials) ?></div>
      <div>
        <div class="user-name-sidebar" id="sidebar-name"><?= htmlspecialchars($sbFull) ?></div>
        <div class="user-role-sidebar"><?= htmlspecialchars($sbRole) ?></div>
      </div>
    </div>
    <button
      onclick="logout()"
      style="
        width: 100%;
        margin-top: 8px;
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.12);
        color: rgba(255,255,255,0.7);
        border-radius: 8px;
        padding: 8px;
        font-size: 13px;
        cursor: pointer;
        font-family: inherit;
      "
      onmouseover="this.style.background='rgba(220,53,69,0.2)';this.style.color='#ff6b7a';"
      onmouseout="this.style.background='rgba(255,255,255,0.08)';this.style.color='rgba(255,255,255,0.7)';"
    >
      🚪 Sign Out
    </button>
  </div>
</aside>

<?php if (!empty($authUser['must_change_password'])): ?>
<!-- Temporary Password Notification Banner -->
<div id="temp-password-banner" style="
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 9999;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 16px 20px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  display: flex;
  align-items: center;
  justify-content: space-between;
  animation: slideDown 0.3s ease-out;
">
  <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
    <span style="font-size: 24px;">🔒</span>
    <div>
      <strong style="display: block; font-size: 15px; margin-bottom: 4px;">Welcome! You're using a temporary password</strong>
      <span style="font-size: 13px; opacity: 0.9;">
        For your security, please update your password in the 
        <a href="profile.php" style="color: white; text-decoration: underline; font-weight: 600;">Profile</a> tab.
      </span>
    </div>
  </div>
  <button onclick="dismissTempPasswordBanner()" style="
    background: rgba(255,255,255,0.2);
    border: 1px solid rgba(255,255,255,0.3);
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.2s;
    white-space: nowrap;
  " onmouseover="this.style.background='rgba(255,255,255,0.3)'"
     onmouseout="this.style.background='rgba(255,255,255,0.2)'">
    Got it
  </button>
</div>
<style>
  @keyframes slideDown {
    from {
      transform: translateY(-100%);
      opacity: 0;
    }
    to {
      transform: translateY(0);
      opacity: 1;
    }
  }
  /* Adjust main content to account for banner */
  main {
    margin-top: 56px !important;
  }
</style>
<script>
  function dismissTempPasswordBanner() {
    const banner = document.getElementById('temp-password-banner');
    banner.style.animation = 'slideDown 0.3s ease-out reverse';
    setTimeout(() => {
      banner.remove();
      document.querySelector('main').style.marginTop = '';
    }, 300);
    
    // Store dismissal in localStorage (optional - banner will show again on next page load)
    localStorage.setItem('temp_password_banner_dismissed', Date.now());
  }
</script>
<?php endif; ?>
