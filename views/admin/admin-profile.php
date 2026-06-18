<?php require_once '../../components/under-construction.php'; ?>
<?php
$pageTitle   = 'Admin Profile — LGU Crop Insurance';
$basePath    = '../../';
$currentPage = 'admin-profile';
$guardRole   = 'admin';
require_once '../../includes/auth-guard.php';
require_once '../../includes/head.php';

$firstName = htmlspecialchars($authUser['first_name'] ?? 'Admin');
$lastName  = htmlspecialchars($authUser['last_name']  ?? '');
$fullName  = trim("$firstName $lastName") ?: 'Admin LGU';
$email     = htmlspecialchars($authUser['email']      ?? '');
$phone     = htmlspecialchars($authUser['phone']      ?? '');
$initials  = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1)) ?: 'AL';
?>
<body>
  <div class="app-layout">

    <?php require_once '../../includes/admin-sidebar.php'; ?>

    <?php
    $topbarTitle    = 'Admin Profile';
    $topbarSubtitle = 'Manage your administrator account';
    $isAdmin        = true;
    require_once '../../includes/topbar.php';
    ?>

    <main class="main-content">
      <div class="page-header">
        <div class="page-header-left">
          <div class="breadcrumb">
            <span>Admin</span><span class="sep">›</span>
            <span class="current">Admin Profile</span>
          </div>
          <h2>Admin Profile</h2>
          <p>View and update your administrative account details</p>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:340px 1fr;gap:24px;align-items:start">

        <!-- Profile Card -->
        <div>
          <div class="card" style="text-align:center;padding:32px 24px 24px">
            <div class="profile-avatar-wrap" style="margin-bottom:16px">
              <div id="profile-initials"
                style="width:96px;height:96px;border-radius:50%;
                  background:linear-gradient(135deg,#e74c3c,#c0392b);
                  display:flex;align-items:center;justify-content:center;
                  font-size:38px;font-weight:800;color:#fff;margin:0 auto;
                  box-shadow:0 6px 20px rgba(231,76,60,0.35)">
                <?= $initials ?>
              </div>
            </div>
            <h3 id="profile-full-name"
              style="font-size:20px;font-weight:800;margin-bottom:4px">
              <?= $fullName ?>
            </h3>
            <p id="profile-email-display"
              style="color:var(--text-muted);font-size:13.5px;margin-bottom:12px">
              <?= $email ?>
            </p>
            <div style="display:inline-flex;align-items:center;gap:6px;
              background:#e8f5e9;border:1px solid #a5d6a7;border-radius:20px;
              padding:4px 14px;font-size:12.5px;font-weight:600;color:var(--primary)">
              🛡️ System Administrator
            </div>
            <div style="margin-top:20px;border-top:1px solid var(--border-color);
              padding-top:18px;text-align:left">
              <div style="display:flex;flex-direction:column;gap:10px;font-size:13px">
                <div style="display:flex;justify-content:space-between">
                  <span style="color:var(--text-muted)">Department</span>
                  <span id="profile-dept" style="font-weight:600">Agricultural Office</span>
                </div>
                <div style="display:flex;justify-content:space-between">
                  <span style="color:var(--text-muted)">Role</span>
                  <span style="font-weight:600">Administrator</span>
                </div>
                <div style="display:flex;justify-content:space-between">
                  <span style="color:var(--text-muted)">LGU Unit</span>
                  <span style="font-weight:600">Sto. Niño</span>
                </div>
                <div style="display:flex;justify-content:space-between">
                  <span style="color:var(--text-muted)">Last Login</span>
                  <span style="font-weight:600" id="last-login">—</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Activity Summary -->
          <div class="card" style="margin-top:0">
            <div class="card-header"><h5>📊 Activity Summary</h5></div>
            <div style="padding:16px;display:flex;flex-direction:column;gap:12px">
              <div style="display:flex;justify-content:space-between;align-items:center;
                padding:10px 12px;background:#f8fafc;border-radius:8px">
                <span style="font-size:13px;color:var(--text-secondary)">📄 Applications Managed</span>
                <span id="act-apps" style="font-weight:800;font-size:15px;color:var(--primary)">0</span>
              </div>
              <div style="display:flex;justify-content:space-between;align-items:center;
                padding:10px 12px;background:#f8fafc;border-radius:8px">
                <span style="font-size:13px;color:var(--text-secondary)">✅ Claims Verified</span>
                <span id="act-claims" style="font-weight:800;font-size:15px;color:var(--success)">0</span>
              </div>
              <div style="display:flex;justify-content:space-between;align-items:center;
                padding:10px 12px;background:#f8fafc;border-radius:8px">
                <span style="font-size:13px;color:var(--text-secondary)">💰 Total Indemnity</span>
                <span id="act-indemnity" style="font-weight:800;font-size:13px;color:var(--secondary-dark)">₱0</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column -->
        <div style="display:flex;flex-direction:column;gap:20px">

          <!-- Edit Profile Card -->
          <div class="card">
            <div class="card-header"><h5>✏️ Edit Profile</h5></div>
            <div style="padding:20px">
              <div class="form-row form-row-2">
                <div class="form-group">
                  <label class="form-label">First Name</label>
                  <input id="edit-fname" type="text" class="form-control"
                    value="<?= $firstName ?>" />
                </div>
                <div class="form-group">
                  <label class="form-label">Last Name</label>
                  <input id="edit-lname" type="text" class="form-control"
                    value="<?= $lastName ?>" />
                </div>
              </div>
              <div class="form-row form-row-2">
                <div class="form-group">
                  <label class="form-label">Email Address</label>
                  <input id="edit-email" type="email" class="form-control"
                    value="<?= $email ?>" />
                </div>
                <div class="form-group">
                  <label class="form-label">Department</label>
                  <select id="edit-dept" class="form-control">
                    <option selected>Agricultural Office</option>
                    <option>Municipal Agriculture Office</option>
                    <option>Office of the Mayor</option>
                    <option>MSWD</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Contact Number</label>
                <input id="edit-phone" type="text" class="form-control"
                  placeholder="09XXXXXXXXX" value="<?= $phone ?>" />
              </div>
              <div style="margin-top:4px">
                <button class="btn btn-primary" onclick="saveProfile()">💾 Save Changes</button>
              </div>
            </div>
          </div>

          <!-- Change Password Card -->
          <div class="card">
            <div class="card-header"><h5>🔐 Change Password</h5></div>
            <div style="padding:20px">
              <div class="form-group">
                <label class="form-label">Current Password</label>
                <div style="position:relative">
                  <input id="cur-pass" type="password" class="form-control"
                    placeholder="Enter current password" style="padding-right:44px" />
                  <span onclick="togglePassword('cur-pass', this)"
                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);
                      cursor:pointer;color:var(--text-muted);font-size:16px">👁</span>
                </div>
              </div>
              <div class="form-row form-row-2">
                <div class="form-group">
                  <label class="form-label">New Password</label>
                  <div style="position:relative">
                    <input id="new-pass" type="password" class="form-control"
                      placeholder="Minimum 8 characters" style="padding-right:44px"
                      oninput="updatePassStrength(this.value)" />
                    <span onclick="togglePassword('new-pass', this)"
                      style="position:absolute;right:12px;top:50%;transform:translateY(-50%);
                        cursor:pointer;color:var(--text-muted);font-size:16px">👁</span>
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label">Confirm New Password</label>
                  <div style="position:relative">
                    <input id="conf-pass" type="password" class="form-control"
                      placeholder="Re-enter new password" style="padding-right:44px" />
                    <span onclick="togglePassword('conf-pass', this)"
                      style="position:absolute;right:12px;top:50%;transform:translateY(-50%);
                        cursor:pointer;color:var(--text-muted);font-size:16px">👁</span>
                  </div>
                </div>
              </div>
              <div id="pass-strength"
                style="height:6px;background:#e9ecef;border-radius:4px;margin-bottom:12px;overflow:hidden">
                <div id="pass-strength-bar"
                  style="height:100%;width:0;border-radius:4px;transition:width 0.3s,background 0.3s"></div>
              </div>
              <button class="btn btn-primary" onclick="changePassword()">🔑 Update Password</button>
            </div>
          </div>

          <!-- Security Info -->
          <div class="card">
            <div class="card-header"><h5>🛡️ Security Information</h5></div>
            <div style="padding:16px 20px;display:flex;flex-direction:column;gap:10px;font-size:13.5px">
              <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;
                background:#e8f5e9;border-radius:8px;border:1px solid #a5d6a7">
                <span>✅</span><span>Two-factor authentication is <strong>enabled</strong> (Prototype Display)</span>
              </div>
              <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;
                background:#e3f2fd;border-radius:8px;border:1px solid #90caf9">
                <span>ℹ️</span><span>Account is protected by LGU Sto. Niño system policies</span>
              </div>
              <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;
                background:#fff3cd;border-radius:8px;border:1px solid #ffc107">
                <span>⚠️</span><span>Password should be changed every <strong>90 days</strong></span>
              </div>
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
      const set     = (id, val) => { const el = document.getElementById(id); if (el) el.value = val || ''; };
      const setText = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val || ''; };
      set('edit-fname', u.first_name || '');
      set('edit-lname', u.last_name  || '');
      set('edit-email', u.email || '');
      set('edit-phone', u.phone || '');
      const fn = u.first_name || 'A';
      const ln = u.last_name  || '';
      setText('profile-full-name',    `${fn} ${ln}`.trim());
      setText('profile-email-display', u.email || '');
      setText('profile-initials', (fn[0] + (ln[0] || '')).toUpperCase());
    }

    async function loadProfile() {
      showLoading();
      try {
        const [profileRes, statsRes] = await Promise.all([
          api('GET', '/auth/me'),
          api('GET', '/dashboard/stats'),
        ]);
        hideLoading();
        populateProfile(profileRes.success ? profileRes.data : user);
        if (statsRes.success) {
          const pol = statsRes.data.policies || {};
          const clm = statsRes.data.claims   || {};
          const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val ?? 0; };
          set('act-apps',   pol.total ?? 0);
          set('act-claims', clm.total ?? 0);
          const indEl = document.getElementById('act-indemnity');
          if (indEl) indEl.textContent = formatCurrency(clm.total_approved_amount ?? 0);
        }
      } catch (err) {
        hideLoading();
        console.error('loadProfile error:', err);
        populateProfile(user);
      }
    }

    async function saveProfile() {
      const get  = id => document.getElementById(id)?.value?.trim() || '';
      const data = { first_name: get('edit-fname'), last_name: get('edit-lname'), phone: get('edit-phone') };
      if (!data.first_name || !data.last_name) {
        showToast('Required', 'First name and last name are required.', 'error');
        return;
      }
      showLoading();
      try {
        const res = await api('PUT', `/users/${user.id}`, data);
        hideLoading();
        if (res.success) {
          const meRes = await api('GET', '/auth/me');
          if (meRes.success) populateProfile(meRes.data);
          initTopbarUser();
          showToast('Saved', 'Profile updated successfully.', 'success');
        } else {
          showToast('Error', res.message || 'Failed to update profile.', 'error');
        }
      } catch (err) {
        hideLoading();
        console.error(err);
        showToast('Error', 'Error saving profile.', 'error');
      }
    }

    function updatePassStrength(val) {
      const bar = document.getElementById('pass-strength-bar');
      if (!bar) return;
      let score = 0;
      if (val.length >= 8)                    score++;
      if (/[A-Z]/.test(val))                  score++;
      if (/[0-9]/.test(val))                  score++;
      if (/[^A-Za-z0-9]/.test(val))           score++;
      const colors = ['#dc3545', '#ffc107', '#17a2b8', '#28a745'];
      bar.style.width      = (score * 25) + '%';
      bar.style.background = colors[score - 1] || '#e9ecef';
    }

    async function changePassword() {
      const current = document.getElementById('cur-pass')?.value  || '';
      const newPass = document.getElementById('new-pass')?.value  || '';
      const confirm = document.getElementById('conf-pass')?.value || '';
      if (!current || !newPass || !confirm) {
        showToast('Required', 'Please fill all password fields.', 'error');
        return;
      }
      if (newPass !== confirm) {
        showToast('Mismatch', 'New passwords do not match.', 'error');
        return;
      }
      if (newPass.length < 8) {
        showToast('Too Short', 'Password must be at least 8 characters.', 'error');
        return;
      }
      showLoading();
      try {
        const res = await api('POST', '/auth/change-password', { current_password: current, new_password: newPass });
        hideLoading();
        if (res.success) {
          showToast('Updated', 'Password changed successfully.', 'success');
          ['cur-pass', 'new-pass', 'conf-pass'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
          });
          const bar = document.getElementById('pass-strength-bar');
          if (bar) { bar.style.width = '0'; bar.style.background = '#e9ecef'; }
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
