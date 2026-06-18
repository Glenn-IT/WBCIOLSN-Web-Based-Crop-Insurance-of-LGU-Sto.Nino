<?php
$pageTitle   = 'User Management — Admin';
$basePath    = '../../';
$currentPage = 'user-management';
$guardRole   = 'admin';
require_once '../../includes/auth-guard.php';
require_once '../../includes/head.php';
?>
<body>
  <div class="app-layout">

    <?php require_once '../../includes/admin-sidebar.php'; ?>

    <?php
    $topbarTitle    = 'User Management';
    $topbarSubtitle = 'Create, update, and manage system users';
    $isAdmin        = true;
    require_once '../../includes/topbar.php';
    ?>

    <main class="main-content">
      <div class="page-header">
        <div class="page-header-left">
          <div class="breadcrumb">
            <span>Admin</span><span class="sep">›</span>
            <span class="current">User Management</span>
          </div>
          <h2>User Management</h2>
          <p>Manage farmer and agent accounts — create, edit, and control access</p>
        </div>
        <div class="page-header-right">
          <button class="btn btn-primary" onclick="openCreateModal()">➕ Add New User</button>
        </div>
      </div>

      <!-- Stats -->
      <div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px">
        <div class="stat-card" style="--stat-color:#1a237e">
          <div class="stat-icon" style="background:#e8eaf6;color:#1a237e">👥</div>
          <div class="stat-info"><h3 id="stat-total">0</h3><p>Total Users</p></div>
        </div>
        <div class="stat-card" style="--stat-color:var(--success)">
          <div class="stat-icon" style="background:#d4edda;color:var(--success)">✅</div>
          <div class="stat-info"><h3 id="stat-active">0</h3><p>Active</p></div>
        </div>
        <div class="stat-card" style="--stat-color:var(--warning)">
          <div class="stat-icon" style="background:#fff3cd;color:#856404">⏸️</div>
          <div class="stat-info"><h3 id="stat-inactive">0</h3><p>Inactive</p></div>
        </div>
        <div class="stat-card" style="--stat-color:var(--danger)">
          <div class="stat-icon" style="background:#f8d7da;color:var(--danger)">🚫</div>
          <div class="stat-info"><h3 id="stat-suspended">0</h3><p>Suspended</p></div>
        </div>
      </div>

      <!-- Table Card -->
      <div class="card">
        <div class="card-header">
          <h5>👥 All Users (Farmers &amp; Agents)</h5>
          <div class="search-filter-bar" style="flex:1;margin-left:20px">
            <div class="search-input-wrapper">
              <span class="search-icon">🔍</span>
              <input type="text" class="form-control" id="search-input"
                placeholder="Search by name, email, or phone..." oninput="filterUsers()" />
            </div>
            <select class="form-select" id="role-filter" onchange="filterUsers()" style="width:140px">
              <option value="">All Roles</option>
              <option value="farmer">Farmer</option>
              <option value="agent">Agent</option>
            </select>
            <select class="form-select" id="status-filter" onchange="filterUsers()" style="width:140px">
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="suspended">Suspended</option>
            </select>
          </div>
        </div>
        <div class="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Status</th>
                <th>Joined</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="users-table-body">
              <tr>
                <td colspan="8" style="text-align:center;color:var(--text-muted);padding:30px">Loading users...</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div id="empty-state" class="empty-state hidden">
          <div class="empty-icon">👥</div>
          <h4>No Users Found</h4>
          <p>Try adjusting your search or filters.</p>
        </div>
        <div id="pagination-bar"
          style="display:flex;align-items:center;justify-content:space-between;
            padding:14px 20px;border-top:1px solid var(--border-color);
            font-size:13px;color:var(--text-muted)">
          <span id="pagination-info">Showing 0 results</span>
          <div style="display:flex;gap:6px" id="pagination-btns"></div>
        </div>
      </div>
    </main>
  </div>

  <!-- Create / Edit User Modal -->
  <div class="modal-overlay" id="user-modal">
    <div class="modal modal-lg">
      <div class="modal-header">
        <h4 id="modal-title">➕ Add New User</h4>
        <button class="modal-close" onclick="closeModal('user-modal')">×</button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="edit-user-id" />
        <div class="form-row form-row-2">
          <div class="form-group">
            <label class="form-label">First Name <span style="color:var(--danger)">*</span></label>
            <input type="text" id="field-first-name" class="form-control" placeholder="e.g. Juan" />
          </div>
          <div class="form-group">
            <label class="form-label">Last Name <span style="color:var(--danger)">*</span></label>
            <input type="text" id="field-last-name" class="form-control" placeholder="e.g. dela Cruz" />
          </div>
        </div>
        <div class="form-row form-row-2">
          <div class="form-group">
            <label class="form-label">Email Address <span style="color:var(--danger)">*</span></label>
            <input type="email" id="field-email" class="form-control" placeholder="user@example.com" />
          </div>
          <div class="form-group">
            <label class="form-label">Phone Number</label>
            <input type="text" id="field-phone" class="form-control" placeholder="e.g. 09XXXXXXXXX" />
          </div>
        </div>
        <div class="form-row form-row-2">
          <div class="form-group">
            <label class="form-label">Role <span style="color:var(--danger)">*</span></label>
            <select id="field-role" class="form-select">
              <option value="farmer">🌾 Farmer</option>
              <option value="agent">🧑‍💼 Agent</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Status</label>
            <select id="field-status" class="form-select">
              <option value="active">✅ Active</option>
              <option value="inactive">⏸️ Inactive</option>
              <option value="suspended">🚫 Suspended</option>
            </select>
          </div>
        </div>
        <div class="form-row form-row-2" id="password-row">
          <div class="form-group">
            <label class="form-label">
              Password <span style="color:var(--danger)" id="password-required">*</span>
            </label>
            <div style="position:relative">
              <input type="password" id="field-password" class="form-control"
                placeholder="Min. 6 characters" style="padding-right:44px" />
              <span onclick="togglePassword('field-password', this)"
                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);cursor:pointer;font-size:16px">👁️</span>
            </div>
            <small id="password-hint" style="color:var(--text-muted);font-size:12px;display:none">Leave blank to keep current password.</small>
          </div>
          <div class="form-group">
            <label class="form-label">
              Confirm Password <span style="color:var(--danger)" id="confirm-required">*</span>
            </label>
            <div style="position:relative">
              <input type="password" id="field-confirm-password" class="form-control"
                placeholder="Re-enter password" style="padding-right:44px" />
              <span onclick="togglePassword('field-confirm-password', this)"
                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);cursor:pointer;font-size:16px">👁️</span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-ghost" onclick="closeModal('user-modal')">Cancel</button>
        <button class="btn btn-primary" id="save-user-btn" onclick="saveUser()">💾 Save User</button>
      </div>
    </div>
  </div>

  <!-- View User Modal -->
  <div class="modal-overlay" id="view-modal">
    <div class="modal" style="max-width:480px">
      <div class="modal-header">
        <h4>👤 User Details</h4>
        <button class="modal-close" onclick="closeModal('view-modal')">×</button>
      </div>
      <div class="modal-body">
        <div style="display:flex;align-items:center;gap:18px;
          background:linear-gradient(135deg,#1a6b3c,#2d9c5a);
          border-radius:12px;padding:20px;margin-bottom:20px;color:white">
          <div id="view-avatar" class="user-avatar"
            style="width:60px;height:60px;font-size:22px;background:rgba(255,255,255,0.2);flex-shrink:0">JD</div>
          <div>
            <div id="view-fullname" style="font-size:18px;font-weight:700">Juan dela Cruz</div>
            <div id="view-role-badge" style="margin-top:4px"></div>
            <div id="view-status-badge" style="margin-top:4px"></div>
          </div>
        </div>
        <div style="display:grid;gap:12px">
          <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border-color)">
            <span style="color:var(--text-muted);font-size:13px">User ID</span>
            <strong id="view-id">#—</strong>
          </div>
          <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border-color)">
            <span style="color:var(--text-muted);font-size:13px">Email</span>
            <strong id="view-email">—</strong>
          </div>
          <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border-color)">
            <span style="color:var(--text-muted);font-size:13px">Phone</span>
            <strong id="view-phone">—</strong>
          </div>
          <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border-color)">
            <span style="color:var(--text-muted);font-size:13px">Email Verified</span>
            <strong id="view-verified">—</strong>
          </div>
          <div style="display:flex;justify-content:space-between;padding:10px 0">
            <span style="color:var(--text-muted);font-size:13px">Joined</span>
            <strong id="view-joined">—</strong>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-ghost" onclick="closeModal('view-modal')">Close</button>
        <button class="btn btn-primary" id="view-edit-btn">✏️ Edit</button>
      </div>
    </div>
  </div>

  <!-- Delete Confirm Modal -->
  <div class="modal-overlay" id="delete-confirm-modal">
    <div class="modal" style="max-width:420px;text-align:center">
      <div class="modal-body" style="padding:36px 32px">
        <div style="font-size:56px;margin-bottom:12px">🗑️</div>
        <h4 style="margin-bottom:8px;color:var(--danger)">Deactivate User?</h4>
        <p id="delete-confirm-msg" style="color:var(--text-muted);font-size:14px;margin-bottom:6px"></p>
        <p style="color:var(--text-muted);font-size:13px;margin-bottom:24px">
          The user will be set to <strong>Inactive</strong> and will lose access to the system.
        </p>
        <div style="display:flex;gap:12px;justify-content:center">
          <button class="btn btn-ghost btn-lg" onclick="closeModal('delete-confirm-modal')">Cancel</button>
          <button class="btn btn-danger btn-lg" id="delete-confirm-btn">🗑️ Yes, Deactivate</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Status Change Modal -->
  <div class="modal-overlay" id="status-modal">
    <div class="modal" style="max-width:400px">
      <div class="modal-header">
        <h4>🔄 Change User Status</h4>
        <button class="modal-close" onclick="closeModal('status-modal')">×</button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="status-user-id" />
        <p id="status-user-name" style="font-weight:600;margin-bottom:14px;font-size:15px"></p>
        <div class="form-group">
          <label class="form-label">New Status</label>
          <select id="status-new-value" class="form-select">
            <option value="active">✅ Active</option>
            <option value="inactive">⏸️ Inactive</option>
            <option value="suspended">🚫 Suspended</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-ghost" onclick="closeModal('status-modal')">Cancel</button>
        <button class="btn btn-primary" onclick="applyStatusChange()">✅ Apply</button>
      </div>
    </div>
  </div>

  <?php require_once '../../includes/toast.php'; ?>
  <script>
    initTopbarUser();

    let allUsers      = [];
    let filteredUsers = [];
    let currentPage   = 1;
    const PER_PAGE    = 10;
    let isEditMode    = false;

    function openCreateModal() {
      isEditMode = false;
      document.getElementById('modal-title').textContent    = '➕ Add New User';
      document.getElementById('save-user-btn').textContent  = '💾 Create User';
      document.getElementById('edit-user-id').value         = '';
      document.getElementById('field-first-name').value     = '';
      document.getElementById('field-last-name').value      = '';
      document.getElementById('field-email').value          = '';
      document.getElementById('field-phone').value          = '';
      document.getElementById('field-role').value           = 'farmer';
      document.getElementById('field-status').value         = 'active';
      document.getElementById('field-password').value       = '';
      document.getElementById('field-confirm-password').value = '';
      document.getElementById('password-hint').style.display     = 'none';
      document.getElementById('password-required').style.display = 'inline';
      document.getElementById('confirm-required').style.display  = 'inline';
      openModal('user-modal');
    }

    function openEditModal(user) {
      isEditMode = true;
      document.getElementById('modal-title').textContent    = '✏️ Edit User';
      document.getElementById('save-user-btn').textContent  = '💾 Save Changes';
      document.getElementById('edit-user-id').value         = user.id;
      document.getElementById('field-first-name').value     = user.first_name || '';
      document.getElementById('field-last-name').value      = user.last_name  || '';
      document.getElementById('field-email').value          = user.email  || '';
      document.getElementById('field-phone').value          = user.phone  || '';
      document.getElementById('field-role').value           = user.role   || 'farmer';
      document.getElementById('field-status').value         = user.status || 'active';
      document.getElementById('field-password').value       = '';
      document.getElementById('field-confirm-password').value = '';
      document.getElementById('password-hint').style.display     = 'block';
      document.getElementById('password-required').style.display = 'none';
      document.getElementById('confirm-required').style.display  = 'none';
      openModal('user-modal');
    }

    function openViewModal(user) {
      const first    = user.first_name || '';
      const last     = user.last_name  || '';
      const initials = (first.charAt(0) + last.charAt(0)).toUpperCase() || '??';
      document.getElementById('view-avatar').textContent    = initials;
      document.getElementById('view-fullname').textContent  = `${first} ${last}`.trim() || '—';
      document.getElementById('view-id').textContent        = '#' + user.id;
      document.getElementById('view-email').textContent     = user.email || '—';
      document.getElementById('view-phone').textContent     = user.phone || '—';
      document.getElementById('view-verified').textContent  = user.email_verified == 1 ? '✅ Verified' : '❌ Not Verified';
      document.getElementById('view-joined').textContent    = formatDate(user.created_at);
      const roleLabels = { farmer: '🌾 Farmer', agent: '🧑‍💼 Agent', admin: '🛡️ Admin' };
      document.getElementById('view-role-badge').innerHTML   = `<span class="badge bg-primary">${roleLabels[user.role] || user.role}</span>`;
      document.getElementById('view-status-badge').innerHTML = getStatusBadge(user.status);
      const editBtn   = document.getElementById('view-edit-btn');
      editBtn.onclick = () => { closeModal('view-modal'); openEditModal(user); };
      openModal('view-modal');
    }

    async function saveUser() {
      const id        = document.getElementById('edit-user-id').value;
      const firstName = document.getElementById('field-first-name').value.trim();
      const lastName  = document.getElementById('field-last-name').value.trim();
      const email     = document.getElementById('field-email').value.trim();
      const phone     = document.getElementById('field-phone').value.trim();
      const role      = document.getElementById('field-role').value;
      const status    = document.getElementById('field-status').value;
      const password  = document.getElementById('field-password').value;
      const confirm   = document.getElementById('field-confirm-password').value;

      if (!firstName || !lastName) {
        showToast('Validation', 'First name and last name are required.', 'error'); return;
      }
      if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showToast('Validation', 'A valid email address is required.', 'error'); return;
      }
      if (!isEditMode && !password) {
        showToast('Validation', 'Password is required when creating a user.', 'error'); return;
      }
      if (password && password.length < 6) {
        showToast('Validation', 'Password must be at least 6 characters.', 'error'); return;
      }
      if (password && password !== confirm) {
        showToast('Validation', 'Passwords do not match.', 'error'); return;
      }

      const body = { first_name: firstName, last_name: lastName, email, phone, role, status };
      if (password) body.password = password;

      showLoading();
      try {
        const res = isEditMode && id
          ? await api('PUT', `/users/${id}`, body)
          : await api('POST', '/users', body);
        hideLoading();
        if (res.success) {
          showToast('Success', isEditMode ? 'User updated successfully.' : 'User created successfully.', 'success');
          closeModal('user-modal');
          await loadUsers();
        } else {
          showToast('Error', res.message || 'Failed to save user.', 'error');
        }
      } catch (err) {
        hideLoading();
        console.error(err);
        showToast('Error', 'An error occurred while saving.', 'error');
      }
    }

    function confirmDelete(user) {
      const name = `${user.first_name} ${user.last_name}`.trim();
      document.getElementById('delete-confirm-msg').textContent = `"${name}" will be deactivated and lose system access.`;
      const btn    = document.getElementById('delete-confirm-btn');
      const newBtn = btn.cloneNode(true);
      btn.parentNode.replaceChild(newBtn, btn);
      newBtn.addEventListener('click', async () => {
        closeModal('delete-confirm-modal');
        showLoading();
        try {
          const res = await api('DELETE', `/users/${user.id}`);
          hideLoading();
          if (res.success) {
            showToast('Success', `${name} has been deactivated.`, 'success');
            await loadUsers();
          } else {
            showToast('Error', res.message || 'Failed to deactivate user.', 'error');
          }
        } catch (err) {
          hideLoading();
          console.error(err);
          showToast('Error', 'An error occurred.', 'error');
        }
      });
      openModal('delete-confirm-modal');
    }

    function openStatusModal(user) {
      document.getElementById('status-user-id').value       = user.id;
      document.getElementById('status-user-name').textContent = `Change status for: ${user.first_name} ${user.last_name}`;
      document.getElementById('status-new-value').value     = user.status || 'active';
      openModal('status-modal');
    }

    async function applyStatusChange() {
      const id     = document.getElementById('status-user-id').value;
      const status = document.getElementById('status-new-value').value;
      if (!id || !status) return;
      showLoading();
      try {
        const res = await api('PUT', `/users/${id}/status`, { status });
        hideLoading();
        if (res.success) {
          showToast('Success', 'User status updated successfully.', 'success');
          closeModal('status-modal');
          await loadUsers();
        } else {
          showToast('Error', res.message || 'Failed to update status.', 'error');
        }
      } catch (err) {
        hideLoading();
        console.error(err);
        showToast('Error', 'An error occurred.', 'error');
      }
    }

    function filterUsers() {
      const q      = (document.getElementById('search-input')?.value || '').toLowerCase();
      const role   = document.getElementById('role-filter')?.value   || '';
      const status = document.getElementById('status-filter')?.value || '';
      filteredUsers = allUsers.filter(u => {
        const name       = `${u.first_name || ''} ${u.last_name || ''}`.toLowerCase();
        const matchText  = !q || name.includes(q) || (u.email || '').toLowerCase().includes(q) || (u.phone || '').toLowerCase().includes(q);
        const matchRole  = !role   || u.role   === role;
        const matchStatus = !status || u.status === status;
        return matchText && matchRole && matchStatus;
      });
      currentPage = 1;
      renderStats();
      renderTable();
      renderPagination();
    }

    function renderStats() {
      const set = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
      set('stat-total',     allUsers.length);
      set('stat-active',    allUsers.filter(u => u.status === 'active').length);
      set('stat-inactive',  allUsers.filter(u => u.status === 'inactive').length);
      set('stat-suspended', allUsers.filter(u => u.status === 'suspended').length);
    }

    function renderTable() {
      const tbody = document.getElementById('users-table-body');
      const empty = document.getElementById('empty-state');
      if (!tbody) return;
      const start    = (currentPage - 1) * PER_PAGE;
      const pageData = filteredUsers.slice(start, start + PER_PAGE);
      if (!filteredUsers.length) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:var(--text-muted);padding:30px">No users found matching your filters.</td></tr>';
        if (empty) empty.classList.remove('hidden');
        return;
      }
      if (empty) empty.classList.add('hidden');
      const roleLabels = { farmer: '🌾 Farmer', agent: '🧑‍💼 Agent' };
      tbody.innerHTML = pageData.map(u => {
        const name        = `${u.first_name || ''} ${u.last_name || ''}`.trim();
        const initials    = ((u.first_name || '').charAt(0) + (u.last_name || '').charAt(0)).toUpperCase() || '??';
        const avatarColor = u.role === 'agent'
          ? 'linear-gradient(135deg,#1a237e,#283593)'
          : 'linear-gradient(135deg,#1a6b3c,#2d9c5a)';
        return `
          <tr>
            <td><strong>#${u.id}</strong></td>
            <td>
              <div style="display:flex;align-items:center;gap:10px">
                <div class="user-avatar" style="width:34px;height:34px;font-size:12px;background:${avatarColor};flex-shrink:0">${initials}</div>
                <div style="font-weight:600">${name || '—'}</div>
              </div>
            </td>
            <td>${u.email || '—'}</td>
            <td>${u.phone || '—'}</td>
            <td><span class="badge bg-primary">${roleLabels[u.role] || u.role}</span></td>
            <td>${getStatusBadge(u.status)}</td>
            <td style="white-space:nowrap">${formatDate(u.created_at)}</td>
            <td style="white-space:nowrap">
              <button class="btn btn-sm btn-outline" title="View Details"  onclick='openViewModal(${JSON.stringify(u)})'>👁️</button>
              <button class="btn btn-sm btn-primary" title="Edit User"     onclick='openEditModal(${JSON.stringify(u)})'>✏️</button>
              <button class="btn btn-sm btn-warning" title="Change Status"
                style="background:#e67e22;border-color:#e67e22;color:white"
                onclick='openStatusModal(${JSON.stringify(u)})'>🔄</button>
              <button class="btn btn-sm btn-danger"  title="Deactivate"    onclick='confirmDelete(${JSON.stringify(u)})'>🗑️</button>
            </td>
          </tr>`;
      }).join('');
    }

    function renderPagination() {
      const total      = filteredUsers.length;
      const totalPages = Math.ceil(total / PER_PAGE);
      const start      = total ? (currentPage - 1) * PER_PAGE + 1 : 0;
      const end        = Math.min(currentPage * PER_PAGE, total);
      const info       = document.getElementById('pagination-info');
      if (info) info.textContent = `Showing ${start}–${end} of ${total} user${total !== 1 ? 's' : ''}`;
      const btns = document.getElementById('pagination-btns');
      if (!btns) return;
      let html = `<button class="btn btn-sm btn-ghost" ${currentPage <= 1 ? 'disabled' : ''} onclick="goPage(${currentPage - 1})">‹ Prev</button>`;
      for (let p = 1; p <= totalPages; p++) {
        html += `<button class="btn btn-sm ${p === currentPage ? 'btn-primary' : 'btn-ghost'}" onclick="goPage(${p})">${p}</button>`;
      }
      html += `<button class="btn btn-sm btn-ghost" ${currentPage >= totalPages ? 'disabled' : ''} onclick="goPage(${currentPage + 1})">Next ›</button>`;
      btns.innerHTML = html;
    }

    function goPage(p) {
      const totalPages = Math.ceil(filteredUsers.length / PER_PAGE);
      if (p < 1 || p > totalPages) return;
      currentPage = p;
      renderTable();
      renderPagination();
    }

    async function loadUsers() {
      showLoading();
      try {
        const res = await api('GET', '/users?per_page=100');
        hideLoading();
        if (!res.success) { showToast('Error', 'Failed to load users.', 'error'); return; }
        allUsers      = res.data || [];
        filteredUsers = [...allUsers];
        renderStats();
        filterUsers();
      } catch (err) {
        hideLoading();
        console.error(err);
        showToast('Error', 'Error loading users.', 'error');
      }
    }

    loadUsers();
  </script>
</body>
</html>
