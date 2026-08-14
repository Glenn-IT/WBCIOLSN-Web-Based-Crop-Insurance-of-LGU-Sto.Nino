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

  <!-- System Action Confirmation Modal -->
  <div class="modal-overlay" id="action-confirm-modal">
    <div class="modal" style="max-width:440px;text-align:center">
      <div class="modal-body" style="padding:32px 28px">
        <div id="confirm-modal-icon" style="font-size:52px;margin-bottom:12px">❓</div>
        <h4 id="confirm-modal-title" style="margin-bottom:8px;color:var(--text-dark,#1e293b);font-size:18px">Confirm Action</h4>
        <p id="confirm-modal-msg" style="color:var(--text-muted);font-size:14px;margin-bottom:20px;white-space:pre-line;line-height:1.5"></p>
        <div style="display:flex;gap:12px;justify-content:center">
          <button class="btn btn-ghost" onclick="closeModal('action-confirm-modal')">Cancel</button>
          <button class="btn btn-primary" id="confirm-modal-btn">Proceed</button>
        </div>
      </div>
    </div>
  </div>

  <!-- System Rejection Prompt Modal -->
  <div class="modal-overlay" id="reject-prompt-modal">
    <div class="modal" style="max-width:460px">
      <div class="modal-header">
        <h4 style="color:var(--danger)">❌ Reject Application — <span id="reject-modal-ref"></span></h4>
        <button class="modal-close" onclick="closeModal('reject-prompt-modal')">×</button>
      </div>
      <div class="modal-body" style="padding:20px">
        <p style="font-size:13.5px;color:var(--text-muted);margin-bottom:14px">
          Please provide a mandatory reason for rejecting this policy application:
        </p>
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label" style="font-weight:600">Rejection Reason / Admin Notes <span style="color:var(--danger)">*</span></label>
          <textarea id="reject-modal-reason" class="form-control" rows="3" placeholder="Enter specific reason for rejection..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-ghost" onclick="closeModal('reject-prompt-modal')">Cancel</button>
        <button class="btn btn-danger" id="reject-modal-confirm-btn">❌ Reject Application</button>
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

    function showSystemConfirm({ icon = '❓', title = 'Confirm Action', message, confirmText = 'Confirm', confirmClass = 'btn-primary' }) {
      return new Promise((resolve) => {
        document.getElementById('confirm-modal-icon').textContent = icon;
        document.getElementById('confirm-modal-title').textContent = title;
        document.getElementById('confirm-modal-msg').textContent = message;

        const btn = document.getElementById('confirm-modal-btn');
        btn.textContent = confirmText;
        btn.className = `btn ${confirmClass}`;

        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);

        newBtn.onclick = () => {
          closeModal('action-confirm-modal');
          resolve(true);
        };

        openModal('action-confirm-modal');
      });
    }

    function showSystemPrompt({ ref }) {
      return new Promise((resolve) => {
        document.getElementById('reject-modal-ref').textContent = ref;
        const input = document.getElementById('reject-modal-reason');
        input.value = '';

        const btn = document.getElementById('reject-modal-confirm-btn');
        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);

        newBtn.onclick = () => {
          const val = input.value.trim();
          if (!val) {
            showToast('Required', 'Please enter a rejection reason.', 'error');
            return;
          }
          closeModal('reject-prompt-modal');
          resolve(val);
        };

        openModal('reject-prompt-modal');
      });
    }

    async function quickDecision(id, status) {
      const app = allApps.find(a => a.id == id);
      const ref = app ? (app.policy_number || 'APP-' + app.id) : 'APP-' + id;

      if (status === 'Approved') {
        const isFarmVer     = (app?.farm_verification     || '').toLowerCase() === 'verified';
        const isDamageVer   = (app?.damage_verification   || '').toLowerCase() === 'verified';
        const isCoverageVer = (app?.coverage_verification || '').toLowerCase() === 'verified';
        const allVer        = isFarmVer && isDamageVer && isCoverageVer;

        if (!allVer) {
          const confirmVerify = await showSystemConfirm({
            icon: '⚠️',
            title: `Approve Application ${ref}?`,
            message: `This application has unverified items:\n` +
              `• Farm Info: ${app?.farm_verification || 'Pending'}\n` +
              `• Damage Report: ${app?.damage_verification || 'Pending'}\n` +
              `• Coverage: ${app?.coverage_verification || 'Pending'}\n\n` +
              `Would you like to mark all verifications as Verified and Approve this application?`,
            confirmText: '✅ Verify All & Approve',
            confirmClass: 'btn-success'
          });
          if (!confirmVerify) return;

          showLoading();
          try {
            const verRes = await api('PUT', `/policies/${id}`, {
              farm_verification: 'Verified',
              damage_verification: 'Verified',
              coverage_verification: 'Verified'
            });
            if (!verRes.success) {
              hideLoading();
              showToast('Error', verRes.message || 'Failed to update verifications.', 'error');
              return;
            }
          } catch (err) {
            hideLoading();
            console.error(err);
            showToast('Error', 'Error updating verifications.', 'error');
            return;
          }
        } else {
          const confirmed = await showSystemConfirm({
            icon: '✅',
            title: 'Approve Application',
            message: `Are you sure you want to approve application ${ref}?`,
            confirmText: '✅ Yes, Approve',
            confirmClass: 'btn-success'
          });
          if (!confirmed) return;
        }

        showLoading();
        try {
          const res = await api('PUT', `/policies/${id}/approve`, { remarks: 'Approved from table actions.' });
          hideLoading();
          if (res.success) {
            showToast('Approved', `Application ${ref} has been approved!`, 'success');
            await loadApplications();
          } else {
            showToast('Error', res.message || 'Failed to approve application.', 'error');
          }
        } catch (err) {
          hideLoading();
          console.error(err);
          showToast('Error', 'Error approving application.', 'error');
        }

      } else if (status === 'UnderReview') {
        const confirmed = await showSystemConfirm({
          icon: '🔍',
          title: 'Set Under Review',
          message: `Set application ${ref} status to Under Review?`,
          confirmText: '🔍 Set Under Review',
          confirmClass: 'btn-primary'
        });
        if (!confirmed) return;

        showLoading();
        try {
          const res = await api('PUT', `/policies/${id}/review`, { remarks: 'Set under review from table actions.' });
          hideLoading();
          if (res.success) {
            showToast('Updated', `Application ${ref} is now Under Review.`, 'success');
            await loadApplications();
          } else {
            showToast('Error', res.message || 'Failed to update status.', 'error');
          }
        } catch (err) {
          hideLoading();
          console.error(err);
          showToast('Error', 'Error updating status.', 'error');
        }

      } else if (status === 'Rejected') {
        const remarks = await showSystemPrompt({ ref });
        if (!remarks) return;

        showLoading();
        try {
          const res = await api('PUT', `/policies/${id}/reject`, { remarks });
          hideLoading();
          if (res.success) {
            showToast('Rejected', `Application ${ref} has been rejected.`, 'success');
            await loadApplications();
          } else {
            showToast('Error', res.message || 'Failed to reject application.', 'error');
          }
        } catch (err) {
          hideLoading();
          console.error(err);
          showToast('Error', 'Error rejecting application.', 'error');
        }

      } else if (status === 'Pending') {
        const confirmed = await showSystemConfirm({
          icon: '⏳',
          title: 'Set to Pending',
          message: `Set application ${ref} back to Pending status?`,
          confirmText: '⏳ Set to Pending',
          confirmClass: 'btn-warning'
        });
        if (!confirmed) return;

        showLoading();
        try {
          const res = await api('PUT', `/policies/${id}`, { status: 'pending', remarks: 'Set to pending from table actions.' });
          hideLoading();
          if (res.success) {
            showToast('Updated', `Application ${ref} set to Pending.`, 'success');
            await loadApplications();
          } else {
            showToast('Error', res.message || 'Failed to update status.', 'error');
          }
        } catch (err) {
          hideLoading();
          console.error(err);
          showToast('Error', 'Error updating status.', 'error');
        }
      }
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
        const currentStatus = (p.status || '').toLowerCase();
        const isApproved    = currentStatus === 'active';
        const isUnderReview = currentStatus === 'under_review';
        const isRejected    = currentStatus === 'rejected';
        const isPending     = currentStatus === 'pending';

        return `
          <tr>
            <td style="vertical-align:middle"><strong>${p.policy_number || 'APP-' + p.id}</strong></td>
            <td style="vertical-align:middle">${(p.first_name || '') + ' ' + (p.last_name || '')}</td>
            <td style="vertical-align:middle">${p.farm_location || p.barangay || p.municipality || '—'}</td>
            <td style="vertical-align:middle">${p.area_hectares || p.land_area || '—'} ha</td>
            <td style="vertical-align:middle">${p.percent_damage ? p.percent_damage + '%' : '—'}</td>
            <td style="vertical-align:middle">${formatCurrency(p.coverage_amount)}</td>
            <td style="vertical-align:middle">${getStatusBadge(p.status)}</td>
            <td style="white-space:nowrap;vertical-align:middle">
              <div style="display:inline-flex;gap:4px;align-items:center">
                <button class="btn btn-sm btn-primary" onclick="openManage(${p.id})" title="Manage details">⚙️ Manage</button>
                <button class="btn btn-sm btn-success" ${isApproved ? 'disabled style="opacity:0.5;cursor:not-allowed"' : ''} onclick="quickDecision(${p.id}, 'Approved')" title="${isApproved ? 'Already Approved' : 'Approve Application'}">✅ Approve</button>
                <button class="btn btn-sm" style="background:#0288d1;color:white;border-color:#0288d1;${isUnderReview ? 'opacity:0.5;cursor:not-allowed' : ''}" ${isUnderReview ? 'disabled' : ''} onclick="quickDecision(${p.id}, 'UnderReview')" title="${isUnderReview ? 'Already Under Review' : 'Set Under Review'}">🔍 Review</button>
                <button class="btn btn-sm btn-danger" ${isRejected ? 'disabled style="opacity:0.5;cursor:not-allowed"' : ''} onclick="quickDecision(${p.id}, 'Rejected')" title="${isRejected ? 'Already Rejected' : 'Reject Application'}">❌ Reject</button>
                <button class="btn btn-sm btn-warning" ${isPending ? 'disabled style="opacity:0.5;cursor:not-allowed"' : ''} onclick="quickDecision(${p.id}, 'Pending')" title="${isPending ? 'Already Pending' : 'Set to Pending'}">⏳ Pending</button>
              </div>
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
