<?php
// Set $currentPage before including to highlight the active nav item.
// Accepted values: 'dashboard', 'new-application', 'my-applications',
//                  'application-status', 'file-claim', 'profile'
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

    <div class="nav-section-label">Account</div>
    <?= userNavItem('profile', '👤', 'My Profile', $currentPage) ?>
  </nav>

  <div class="sidebar-footer">
    <div class="user-info-sidebar" onclick="navigateTo('profile.php')">
      <div class="user-avatar" id="sidebar-avatar">--</div>
      <div>
        <div class="user-name-sidebar" id="sidebar-name">Loading...</div>
        <div class="user-role-sidebar">Farmer</div>
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
<div class="modal-overlay active" id="force-password-modal" style="z-index:9999">
  <div class="modal" style="max-width:440px">
    <div class="modal-header">
      <h4>🔒 Change Your Password</h4>
    </div>
    <div class="modal-body">
      <p style="color:var(--text-muted);font-size:14px;margin-bottom:16px">
        For your security, you must set a new password before continuing.
        Enter the temporary password you were emailed as your current password.
      </p>
      <div class="form-group">
        <label class="form-label">Temporary / Current Password</label>
        <input type="password" id="force-current-pass" class="form-control" />
      </div>
      <div class="form-group">
        <label class="form-label">New Password</label>
        <input type="password" id="force-new-pass" class="form-control" placeholder="Min. 8 characters" />
      </div>
      <div class="form-group">
        <label class="form-label">Confirm New Password</label>
        <input type="password" id="force-confirm-pass" class="form-control" />
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-primary" style="width:100%" onclick="submitForcedPasswordChange()">Set New Password</button>
    </div>
  </div>
</div>
<script>
  document.body.style.overflow = 'hidden';

  async function submitForcedPasswordChange() {
    const current = document.getElementById('force-current-pass').value;
    const newPass = document.getElementById('force-new-pass').value;
    const confirm = document.getElementById('force-confirm-pass').value;
    if (!current || !newPass || !confirm) {
      showToast('Validation', 'Please fill all fields.', 'error'); return;
    }
    if (newPass !== confirm) {
      showToast('Validation', 'New passwords do not match.', 'error'); return;
    }
    if (newPass.length < 8) {
      showToast('Validation', 'Password must be at least 8 characters.', 'error'); return;
    }
    showLoading();
    try {
      const res = await api('POST', '/auth/change-password', {
        current_password: current,
        new_password:      newPass,
      });
      hideLoading();
      if (res.success) {
        showToast('Success', 'Password updated. Welcome!', 'success');
        document.getElementById('force-password-modal').remove();
        document.body.style.overflow = '';
        const user = JSON.parse(localStorage.getItem('lgu_current_user') || 'null');
        if (user) {
          user.must_change_password = 0;
          localStorage.setItem('lgu_current_user', JSON.stringify(user));
        }
      } else {
        showToast('Error', res.message || 'Failed to change password.', 'error');
      }
    } catch (err) {
      hideLoading();
      console.error(err);
      showToast('Error', 'An error occurred while changing your password.', 'error');
    }
  }
</script>
<?php endif; ?>
