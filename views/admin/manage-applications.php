<?php require_once '../../components/under-construction.php'; ?>
<?php
$pageTitle   = 'Manage Applications — Admin';
$basePath    = '../../';
$currentPage = 'manage-applications';
$guardRole   = 'admin';
require_once '../../includes/auth-guard.php';
require_once '../../includes/head.php';
?>
<body>
  <div class="app-layout">

    <?php require_once '../../includes/admin-sidebar.php'; ?>

    <?php
    $topbarTitle    = 'Manage Applications';
    $topbarSubtitle = 'Review, approve, reject, and edit applications';
    $isAdmin        = true;
    require_once '../../includes/topbar.php';
    ?>

    <main class="main-content">
      <div class="page-header">
        <div class="page-header-left">
          <div class="breadcrumb">
            <span>Admin</span><span class="sep">›</span>
            <span class="current">Manage Applications</span>
          </div>
          <h2>Manage Applications</h2>
          <p>Approve, reject, or update application details and verification status</p>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h5>⚙️ Application Management</h5>
          <div class="search-filter-bar" style="flex:1;margin-left:20px">
            <div class="search-input-wrapper">
              <span class="search-icon">🔍</span>
              <input type="text" class="form-control" id="search-input"
                placeholder="Search applications..." oninput="filterApps()" />
            </div>
            <select class="form-select" id="status-filter" onchange="filterApps()" style="width:160px">
              <option value="">All Status</option>
              <option value="pending">Pending</option>
              <option value="under_review">Under Review</option>
              <option value="active">Approved</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
        </div>
        <div class="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>App ID</th>
                <th>Farmer</th>
                <th>Location</th>
                <th>Area</th>
                <th>Damage %</th>
                <th>Coverage Req.</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="apps-table-body"></tbody>
          </table>
        </div>
        <div id="empty-state" class="empty-state hidden">
          <div class="empty-icon">📋</div>
          <h4>No Applications Found</h4>
        </div>
      </div>
    </main>
  </div>

  <!-- Delete Confirmation Modal -->
  <div class="modal-overlay" id="delete-confirm-modal">
    <div class="modal" style="max-width:420px;text-align:center">
      <div class="modal-body" style="padding:36px 32px">
        <div style="font-size:56px;margin-bottom:12px">🗑️</div>
        <h4 style="margin-bottom:8px;color:var(--danger,#dc3545)">Delete Application?</h4>
        <p id="delete-confirm-msg" style="color:var(--text-muted);font-size:14px;margin-bottom:6px"></p>
        <p style="color:var(--text-muted);font-size:13px;margin-bottom:24px">
          This action <strong>cannot be undone</strong>.
        </p>
        <div style="display:flex;gap:12px;justify-content:center">
          <button class="btn btn-ghost btn-lg" onclick="closeModal('delete-confirm-modal')">Cancel</button>
          <button class="btn btn-danger btn-lg" id="delete-confirm-btn">🗑️ Yes, Delete</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit/Update Modal -->
  <div class="modal-overlay" id="manage-modal">
    <div class="modal modal-lg">
      <div class="modal-header">
        <h4>⚙️ Manage Application — <span id="modal-app-id"></span></h4>
        <button class="modal-close" onclick="closeModal('manage-modal')">×</button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="edit-id" />

        <!-- Current Status Banner -->
        <div id="current-status-banner"
          style="background:#f8fafc;border:1px solid var(--border-color);border-radius:10px;
            padding:12px 18px;margin-bottom:18px;display:flex;align-items:center;gap:12px">
          <span style="font-size:13px;color:var(--text-muted)">Current Status:</span>
          <span id="current-status-badge"></span>
          <span id="current-remarks-display" style="font-size:12px;color:var(--text-muted);font-style:italic"></span>
        </div>

        <!-- Application Decision -->
        <div style="background:linear-gradient(135deg,#e8eaf6,#f0f4ff);border-radius:10px;
          padding:18px;margin-bottom:20px">
          <div class="form-section-title" style="margin-bottom:12px">🏛️ Application Decision</div>
          <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
            <button id="btn-approve" class="btn btn-success" onclick="updateStatus('Approved')"
              disabled title="All 3 verifications must be Verified first">✅ Approve</button>
            <button class="btn btn-primary" style="background:#0288d1;border-color:#0288d1"
              onclick="updateStatus('UnderReview')">🔍 Set Under Review</button>
            <button class="btn btn-danger" onclick="updateStatus('Rejected')">❌ Reject</button>
            <button class="btn btn-warning" onclick="updateStatus('Pending')">⏳ Set to Pending</button>
          </div>
          <div id="approve-hint" style="margin-top:8px;font-size:12px;color:var(--text-muted)">
            ⚠️ Approve button will unlock once Farm, Damage, and Coverage verifications
            are all set to <strong>Verified</strong>.
          </div>
        </div>

        <!-- Verification Status -->
        <div id="verification-section">
          <div class="form-section-title">🔍 Update Verification Status</div>
          <div class="form-row form-row-3">
            <div class="form-group">
              <label class="form-label">Farm Info Verification</label>
              <select id="edit-farm-ver" class="form-select" onchange="checkApproveEligibility()">
                <option value="Pending">⏳ Pending</option>
                <option value="Verified">✅ Verified</option>
                <option value="Rejected">❌ Rejected</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Damage Report Verification</label>
              <select id="edit-damage-ver" class="form-select" onchange="checkApproveEligibility()">
                <option value="Pending">⏳ Pending</option>
                <option value="Verified">✅ Verified</option>
                <option value="Rejected">❌ Rejected</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Coverage Verification</label>
              <select id="edit-coverage-ver" class="form-select" onchange="checkApproveEligibility()">
                <option value="Pending">⏳ Pending</option>
                <option value="Verified">✅ Verified</option>
                <option value="Rejected">❌ Rejected</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Edit Fields -->
        <div class="form-section-title" style="margin-top:8px">✏️ Edit Application Data</div>
        <div class="form-row form-row-2">
          <div class="form-group">
            <label class="form-label">Farmer Name</label>
            <input type="text" id="edit-farmer-name" class="form-control" />
          </div>
          <div class="form-group">
            <label class="form-label">Farm Location</label>
            <input type="text" id="edit-location" class="form-control" />
          </div>
        </div>
        <div class="form-row form-row-3">
          <div class="form-group">
            <label class="form-label">Total Area (ha)</label>
            <input type="number" id="edit-area" class="form-control" />
          </div>
          <div class="form-group">
            <label class="form-label">% Damage</label>
            <input type="number" id="edit-percent" class="form-control" />
          </div>
          <div class="form-group">
            <label class="form-label">Desired Coverage (₱)</label>
            <input type="number" id="edit-coverage" class="form-control" />
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Admin Notes / Remarks</label>
          <textarea id="edit-notes" class="form-control"
            placeholder="Internal notes or rejection reason..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-ghost" onclick="closeModal('manage-modal')">Cancel</button>
        <button class="btn btn-primary" onclick="saveManage()">💾 Save Changes</button>
      </div>
    </div>
  </div>

  <?php require_once '../../includes/toast.php'; ?>
  <script>
    initTopbarUser();

    let allApps     = [];
    let selectedApp = null;

    function checkApproveEligibility() {
      const farm     = document.getElementById('edit-farm-ver')?.value;
      const damage   = document.getElementById('edit-damage-ver')?.value;
      const coverage = document.getElementById('edit-coverage-ver')?.value;
      const allVerified = farm === 'Verified' && damage === 'Verified' && coverage === 'Verified';
      const btn  = document.getElementById('btn-approve');
      const hint = document.getElementById('approve-hint');
      if (btn)  { btn.disabled = !allVerified; btn.style.opacity = allVerified ? '1' : '0.5'; }
      if (hint) { hint.style.display = allVerified ? 'none' : 'block'; }
    }

    function syncVerificationSection(status) {
      const sec = document.getElementById('verification-section');
      if (!sec) return;
      sec.style.display = (status || '').toLowerCase() === 'under_review' ? 'block' : 'none';
    }

    function filterApps() {
      const q = (document.getElementById('search-input')?.value  || '').toLowerCase();
      const s = (document.getElementById('status-filter')?.value || '').toLowerCase();
      const filtered = allApps.filter(p => {
        const matchText = !q ||
          (p.policy_number || '').toLowerCase().includes(q) ||
          ((p.first_name || '') + ' ' + (p.last_name || '')).toLowerCase().includes(q) ||
          (p.farm_location || p.barangay || '').toLowerCase().includes(q);
        const matchStatus = !s || (p.status || '').toLowerCase() === s;
        return matchText && matchStatus;
      });
      renderTable(filtered);
    }

    async function openManage(id) {
      showLoading();
      try {
        const res = await api('GET', `/policies/${id}`);
        hideLoading();
        if (!res.success) { showToast('Error', 'Failed to load application.', 'error'); return; }
        selectedApp = res.data;
        const p = selectedApp;

        document.getElementById('edit-id').value            = p.id;
        document.getElementById('modal-app-id').textContent = p.policy_number || 'APP-' + p.id;

        document.getElementById('current-status-badge').innerHTML = getStatusBadge(p.status);
        const remarksEl = document.getElementById('current-remarks-display');
        remarksEl.textContent = p.remarks ? `💬 ${p.remarks}` : '';

        syncVerificationSection(p.status);
        setSelectValue('edit-farm-ver',     p.farm_verification     || 'Pending');
        setSelectValue('edit-damage-ver',   p.damage_verification   || 'Pending');
        setSelectValue('edit-coverage-ver', p.coverage_verification || 'Pending');
        checkApproveEligibility();

        document.getElementById('edit-farmer-name').value = ((p.first_name || '') + ' ' + (p.last_name || '')).trim();
        document.getElementById('edit-location').value    = p.farm_location || p.barangay || '';
        document.getElementById('edit-area').value        = p.area_hectares || p.land_area || '';
        document.getElementById('edit-percent').value     = p.percent_damage || '';
        document.getElementById('edit-coverage').value    = p.coverage_amount || '';
        document.getElementById('edit-notes').value       = p.remarks || '';

        openModal('manage-modal');
      } catch (err) {
        hideLoading();
        console.error(err);
        showToast('Error', 'Error loading application.', 'error');
      }
    }

    function setSelectValue(elId, value) {
      const el = document.getElementById(elId);
      if (!el) return;
      for (let opt of el.options) {
        if (opt.value === value) { el.value = value; return; }
      }
      el.value = el.options[0].value;
    }

    async function updateStatus(status) {
      const id = document.getElementById('edit-id')?.value;
      if (!id) return;
      const remarks = document.getElementById('edit-notes')?.value?.trim() || '';

      if (status === 'Rejected' && !remarks) {
        showToast('Required', 'Please enter a rejection reason in the Admin Notes field.', 'error');
        return;
      }

      let endpoint, body;
      if (status === 'Approved')         { endpoint = `/policies/${id}/approve`; body = { remarks: remarks || 'Approved by admin.' }; }
      else if (status === 'Rejected')    { endpoint = `/policies/${id}/reject`;  body = { remarks }; }
      else if (status === 'UnderReview') { endpoint = `/policies/${id}/review`;  body = { remarks }; }
      else                               { endpoint = `/policies/${id}`;          body = { status: 'pending', remarks }; }

      showLoading();
      try {
        const res = await api('PUT', endpoint, body);
        hideLoading();
        if (res.success) {
          const newStatus = res.data?.status || '';
          document.getElementById('current-status-badge').innerHTML = getStatusBadge(newStatus);
          document.getElementById('current-remarks-display').textContent = remarks ? `💬 ${remarks}` : '';
          syncVerificationSection(newStatus);
          if (selectedApp) selectedApp.status = newStatus;
          const label = status === 'UnderReview' ? 'Under Review' : status;
          showToast('Updated', `Status updated to ${label}.`, 'success');
          await loadApplications();
        } else {
          showToast('Error', res.message || 'Failed to update status.', 'error');
        }
      } catch (err) {
        hideLoading();
        console.error(err);
        showToast('Error', 'Error updating application.', 'error');
      }
    }

    async function saveManage() {
      const id = document.getElementById('edit-id')?.value;
      if (!id) return;
      const body = {
        farm_location:         document.getElementById('edit-location')?.value    || '',
        coverage_amount:  parseFloat(document.getElementById('edit-coverage')?.value) || 0,
        percent_damage:   parseFloat(document.getElementById('edit-percent')?.value)  || 0,
        remarks:               document.getElementById('edit-notes')?.value        || '',
        farm_verification:     document.getElementById('edit-farm-ver')?.value     || 'Pending',
        damage_verification:   document.getElementById('edit-damage-ver')?.value   || 'Pending',
        coverage_verification: document.getElementById('edit-coverage-ver')?.value || 'Pending',
      };
      showLoading();
      try {
        const res = await api('PUT', `/policies/${id}`, body);
        hideLoading();
        if (res.success) {
          showToast('Saved', 'Application updated successfully.', 'success');
          closeModal('manage-modal');
          await loadApplications();
        } else {
          showToast('Error', res.message || 'Failed to save changes.', 'error');
        }
      } catch (err) {
        hideLoading();
        console.error(err);
        showToast('Error', 'Error saving changes.', 'error');
      }
    }

    async function deleteApp(id, ref) {
      document.getElementById('delete-confirm-msg').textContent =
        `Application ${ref} will be permanently removed.`;
      openModal('delete-confirm-modal');

      const btn    = document.getElementById('delete-confirm-btn');
      const newBtn = btn.cloneNode(true);
      btn.parentNode.replaceChild(newBtn, btn);
      newBtn.addEventListener('click', async () => {
        closeModal('delete-confirm-modal');
        showLoading();
        try {
          const res = await api('DELETE', `/policies/${id}`);
          hideLoading();
          if (res.success) {
            showToast('Deleted', `Application ${ref} deleted.`, 'success');
            await loadApplications();
          } else {
            showToast('Error', res.message || 'Failed to delete.', 'error');
          }
        } catch (err) {
          hideLoading();
          console.error(err);
          showToast('Error', 'Error deleting application.', 'error');
        }
      });
    }

    function renderTable(apps) {
      const tbody = document.getElementById('apps-table-body');
      const empty = document.getElementById('empty-state');
      if (!tbody) return;
      if (!apps.length) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:var(--text-muted);padding:20px">No applications found.</td></tr>';
        if (empty) empty.classList.remove('hidden');
        return;
      }
      if (empty) empty.classList.add('hidden');
      tbody.innerHTML = apps.map(p => {
        const ref = (p.policy_number || 'APP-' + p.id).replace(/"/g, '');
        return `
          <tr>
            <td><strong>${p.policy_number || 'APP-' + p.id}</strong></td>
            <td>${(p.first_name || '') + ' ' + (p.last_name || '')}</td>
            <td>${p.farm_location || p.barangay || p.municipality || '—'}</td>
            <td>${p.area_hectares || p.land_area || '—'} ha</td>
            <td>${p.percent_damage ? p.percent_damage + '%' : '—'}</td>
            <td>${formatCurrency(p.coverage_amount)}</td>
            <td>${getStatusBadge(p.status)}</td>
            <td style="white-space:nowrap">
              <button class="btn btn-sm btn-primary" onclick="openManage(${p.id})">⚙️ Manage</button>
              <button class="btn btn-sm btn-danger" onclick="deleteApp(${p.id}, &quot;${ref}&quot;)">🗑️</button>
            </td>
          </tr>`;
      }).join('');
    }

    async function loadApplications() {
      try {
        const res = await api('GET', '/policies');
        if (!res.success) { showToast('Error', 'Failed to load applications.', 'error'); return; }
        allApps = res.data || [];
        filterApps();
      } catch (err) {
        console.error(err);
        showToast('Error', 'Error loading applications.', 'error');
      }
    }

    loadApplications();
  </script>
</body>
</html>
