<?php
/**
 * Reusable Logout Confirmation Modal
 * Included automatically in user-sidebar.php and admin-sidebar.php
 */
?>
<!-- Logout Confirmation Modal -->
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
</div>
