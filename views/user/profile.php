<?php
$pageTitle   = 'My Profile — Crop Insurance';
$basePath    = '../../';
$currentPage = 'profile';
$guardRole   = 'user';
require_once '../../includes/auth-guard.php';
require_once '../../includes/head.php';

$firstName = htmlspecialchars($authUser['first_name'] ?? '');
$lastName  = htmlspecialchars($authUser['last_name']  ?? '');
$fullName  = trim("$firstName $lastName") ?: 'Farmer';
$initials  = strtoupper(
  substr($authUser['first_name'] ?? 'F', 0, 1) .
  substr($authUser['last_name']  ?? '',  0, 1)
);
?>
<body>
  <div class="app-layout">

    <?php require_once '../../includes/user-sidebar.php'; ?>

    <?php
    $topbarTitle    = 'My Profile';
    $topbarSubtitle = 'Manage your personal information';
    $isAdmin        = false;
    require_once '../../includes/topbar.php';
    ?>

    <main class="main-content">
      <div class="page-header">
        <div class="page-header-left">
          <div class="breadcrumb">
            <span>Home</span><span class="sep">›</span>
            <span class="current">My Profile</span>
          </div>
          <h2>My Profile</h2>
        </div>
      </div>

      <div class="grid-2" style="align-items:start">
        <!-- Profile Card -->
        <div class="card" style="overflow:hidden">
          <div class="profile-header">
            <div class="profile-avatar-large" id="profile-avatar"
              onclick="showToast('Info', 'Photo upload is not available yet.', 'info')">
              <?= $initials ?>
            </div>
            <h3 id="profile-full-name" style="font-size:20px;font-weight:700;margin-bottom:4px">
              <?= $fullName ?>
            </h3>
            <p id="profile-farmer-type" style="opacity:0.8;font-size:13.5px">Farmer</p>
            <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,0.15);
              border-radius:20px;padding:6px 14px;margin-top:10px;font-size:12.5px">
              ✅ Registered Farmer
            </div>
          </div>
          <div class="card-body">
            <div style="display:flex;flex-direction:column;gap:12px">
              <div style="display:flex;align-items:center;gap:12px;padding:10px;background:#f8fafc;border-radius:8px">
                <span style="font-size:20px">📧</span>
                <div>
                  <div style="font-size:11px;color:var(--text-muted);font-weight:600;text-transform:uppercase">Email</div>
                  <div id="profile-email" style="font-size:14px;font-weight:600">
                    <?= htmlspecialchars($authUser['email'] ?? '—') ?>
                  </div>
                </div>
              </div>
              <div style="display:flex;align-items:center;gap:12px;padding:10px;background:#f8fafc;border-radius:8px">
                <span style="font-size:20px">📱</span>
                <div>
                  <div style="font-size:11px;color:var(--text-muted);font-weight:600;text-transform:uppercase">Phone</div>
                  <div id="profile-phone" style="font-size:14px;font-weight:600">
                    <?= htmlspecialchars($authUser['phone'] ?? $authUser['contact_number'] ?? '—') ?>
                  </div>
                </div>
              </div>
              <div style="display:flex;align-items:center;gap:12px;padding:10px;background:#f8fafc;border-radius:8px">
                <span style="font-size:20px">📍</span>
                <div>
                  <div style="font-size:11px;color:var(--text-muted);font-weight:600;text-transform:uppercase">Address</div>
                  <div id="profile-address" style="font-size:14px;font-weight:600">
                    <?= htmlspecialchars($authUser['address'] ?? '—') ?>
                  </div>
                </div>
              </div>
              <div style="display:flex;align-items:center;gap:12px;padding:10px;background:#f8fafc;border-radius:8px">
                <span style="font-size:20px">📅</span>
                <div>
                  <div style="font-size:11px;color:var(--text-muted);font-weight:600;text-transform:uppercase">Member Since</div>
                  <div id="profile-created" style="font-size:14px;font-weight:600">—</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Edit Form -->
        <div>
          <div class="card" style="margin-bottom:20px">
            <div class="card-header"><h5>✏️ Edit Profile</h5></div>
            <div class="card-body">
              <div class="form-row form-row-2">
                <div class="form-group">
                  <label class="form-label">First Name</label>
                  <input type="text" id="edit-fname" class="form-control"
                    value="<?= $firstName ?>" />
                </div>
                <div class="form-group">
                  <label class="form-label">Last Name</label>
                  <input type="text" id="edit-lname" class="form-control"
                    value="<?= $lastName ?>" />
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" id="edit-email" class="form-control"
                  value="<?= htmlspecialchars($authUser['email'] ?? '') ?>" />
              </div>
              <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="tel" id="edit-phone" class="form-control"
                  value="<?= htmlspecialchars($authUser['phone'] ?? $authUser['contact_number'] ?? '') ?>" />
              </div>
              <div class="form-group">
                <label class="form-label">Address</label>
                <input type="text" id="edit-address" class="form-control"
                  value="<?= htmlspecialchars($authUser['address'] ?? '') ?>" />
              </div>
              <div class="form-group">
                <label class="form-label">Farmer Category</label>
                <select id="edit-farmer-type" class="form-select">
                  <option>Small Farmer</option>
                  <option>Commercial Farmer</option>
                  <option>Agrarian Reform Beneficiary</option>
                  <option>Fisherfolk</option>
                </select>
              </div>
              <button class="btn btn-primary w-100" onclick="saveProfile()">
                💾 Save Profile Changes
              </button>
            </div>
          </div>

          <!-- Change Password -->
          <div class="card">
            <div class="card-header"><h5>🔒 Change Password</h5></div>
            <div class="card-body">
              <div class="form-group">
                <label class="form-label">Current Password</label>
                <div class="input-group">
                  <span class="input-icon">🔒</span>
                  <input type="password" id="current-pass" class="form-control"
                    placeholder="Enter current password" />
                  <span class="input-icon-right"
                    onclick="togglePassword('current-pass', this)">👁️</span>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">New Password</label>
                <input type="password" id="new-pass" class="form-control"
                  placeholder="Min. 8 characters" />
              </div>
              <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input type="password" id="confirm-pass" class="form-control"
                  placeholder="Repeat new password" />
              </div>
              <button class="btn btn-outline w-100" onclick="changePassword()">
                🔑 Update Password
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <?php require_once '../../includes/toast.php'; ?>
  <script>
    const user = <?= json_encode($authUser) ?>;
    initTopbarUser();

    function populateProfile(u) {
      const setVal  = (id, val) => { const el = document.getElementById(id); if (el) el.value = val || ''; };
      const setText = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val || '—'; };

      setVal('edit-fname',       u.first_name || u.firstName || '');
      setVal('edit-lname',       u.last_name  || u.lastName  || '');
      setVal('edit-email',       u.email      || '');
      setVal('edit-phone',       u.phone      || u.contact_number || '');
      setVal('edit-address',     u.address    || '');

      const ftSelect = document.getElementById('edit-farmer-type');
      if (ftSelect && (u.farmer_type || u.farmerType)) ftSelect.value = u.farmer_type || u.farmerType;

      setText('profile-email',        u.email   || '');
      setText('profile-phone',        u.phone   || u.contact_number || '');
      setText('profile-address',      u.address || '');
      setText('profile-farmer-type',  u.farmer_type || u.farmerType || 'Farmer');
      setText('profile-created',      typeof formatDate === 'function' ? formatDate(u.created_at) : (u.created_at || ''));

      const fn       = u.first_name || u.firstName || 'F';
      const ln       = u.last_name  || u.lastName  || '';
      const fullName = (fn + ' ' + ln).trim();
      const initials = (fn[0] + (ln[0] || '')).toUpperCase();

      setText('profile-full-name', fullName);
      const avatarEl = document.getElementById('profile-avatar');
      if (avatarEl) avatarEl.textContent = initials;
    }

    async function loadProfile() {
      try {
        const res = await api('GET', '/auth/me');
        populateProfile(res.success ? res.data : user);
      } catch {
        populateProfile(user);
      }
    }

    async function saveProfile() {
      const get = id => document.getElementById(id)?.value?.trim() || '';
      const data = {
        first_name:   get('edit-fname'),
        last_name:    get('edit-lname'),
        phone:        get('edit-phone'),
        address:      get('edit-address'),
        farmer_type:  document.getElementById('edit-farmer-type')?.value || '',
      };
      if (!data.first_name || !data.last_name) {
        showToast('Validation', 'First name and last name are required.', 'error');
        return;
      }
      showLoading();
      try {
        const res = await api('PUT', `/users/${user.id}`, data);
        hideLoading();
        if (res.success) {
          showToast('Saved', 'Profile updated successfully.', 'success');
          const fresh = await api('GET', '/auth/me');
          populateProfile(fresh.success ? fresh.data : { ...user, ...data });
          initTopbarUser();
        } else {
          showToast('Error', res.message || 'Failed to update profile.', 'error');
        }
      } catch (err) {
        hideLoading();
        console.error(err);
        showToast('Error', 'Error saving profile.', 'error');
      }
    }

    async function changePassword() {
      const current = document.getElementById('current-pass')?.value;
      const newPass = document.getElementById('new-pass')?.value;
      const confirm = document.getElementById('confirm-pass')?.value;
      if (!current || !newPass || !confirm) {
        showToast('Validation', 'Please fill all password fields.', 'error'); return;
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
          new_password:     newPass,
        });
        hideLoading();
        if (res.success) {
          showToast('Success', 'Password changed successfully.', 'success');
          ['current-pass', 'new-pass', 'confirm-pass'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
          });
        } else {
          showToast('Error', res.message || 'Failed to change password.', 'error');
        }
      } catch (err) {
        hideLoading();
        console.error(err);
        showToast('Error', 'Error changing password.', 'error');
      }
    }

    loadProfile();
  </script>
</body>
</html>
