<?php
// Set $currentPage before including to highlight the active nav item.
// Accepted values: 'dashboard', 'view-applications', 'manage-applications',
//                  'claim-verification', 'user-management', 'reports', 'admin-profile'
$currentPage = $currentPage ?? '';

function adminNavItem(string $page, string $icon, string $label, string $current, string $badgeId = ''): string {
    $active = ($current === $page) ? ' active' : '';
    $badge  = $badgeId ? '<span class="nav-badge" id="' . $badgeId . '">—</span>' : '';
    return '<div class="nav-item' . $active . '" onclick="navigateTo(\'' . $page . '.php\')">'
         . '<span class="nav-icon">' . $icon . '</span>' . htmlspecialchars($label) . $badge
         . '</div>';
}
?>
<aside class="sidebar admin-sidebar" id="sidebar">
  <div class="sidebar-header">
    <div class="sidebar-logo">🛡️</div>
    <div>
      <div class="sidebar-title">LGU Admin Panel</div>
      <div class="sidebar-subtitle">Agricultural Office</div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section-label">Overview</div>
    <?= adminNavItem('dashboard', '📊', 'Dashboard', $currentPage) ?>

    <div class="nav-section-label">Management</div>
    <?= adminNavItem('view-applications',   '📋', 'View Applications',   $currentPage) ?>
    <?= adminNavItem('manage-applications', '⚙️',  'Manage Applications', $currentPage) ?>
    <?= adminNavItem('claim-verification',  '✅', 'Claim Verification',  $currentPage, 'pending-badge') ?>
    <?= adminNavItem('user-management',     '👥', 'User Management',     $currentPage) ?>
    <?= adminNavItem('sms-logs',            '📱', 'SMS Logs',            $currentPage) ?>

    <div class="nav-section-label">Reports & Settings</div>
    <?= adminNavItem('reports',       '📈', 'Reports',       $currentPage) ?>
    <?= adminNavItem('admin-profile', '👤', 'Admin Profile', $currentPage) ?>
  </nav>

  <div class="sidebar-footer">
    <div class="user-info-sidebar">
      <div class="user-avatar" style="background: linear-gradient(135deg, #e74c3c, #c0392b)" id="sidebar-avatar">--</div>
      <div>
        <div class="user-name-sidebar" id="sidebar-name">Loading...</div>
        <div class="user-role-sidebar">Administrator</div>
      </div>
    </div>
    <button
      onclick="adminLogout()"
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
