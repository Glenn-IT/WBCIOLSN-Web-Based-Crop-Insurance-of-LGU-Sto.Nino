<?php
$pageTitle   = 'Claim Verification — Admin';
$basePath    = '../../';
$currentPage = 'claim-verification';
$guardRole   = 'admin';
require_once '../../includes/auth-guard.php';
require_once '../../includes/head.php';
?>
<body>
  <div class="app-layout">

    <?php require_once '../../includes/admin-sidebar.php'; ?>

    <?php
    $topbarTitle    = 'Claim Verification';
    $topbarSubtitle = 'Review and process insurance claims';
    $isAdmin        = true;
    require_once '../../includes/topbar.php';
    ?>

    <main class="main-content">
      <div class="page-header">
        <div class="page-header-left">
          <div class="breadcrumb">
            <span>Admin</span><span class="sep">›</span>
            <span class="current">Claim Verification</span>
          </div>
          <h2>Insurance Claim Verification</h2>
          <p>Approve or reject submitted insurance claims</p>
        </div>
      </div>

      <!-- Stats -->
      <div class="stats-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:24px">
        <div class="stat-card" style="--stat-color:var(--warning)">
          <div class="stat-icon" style="background:#fff3cd;color:#856404">⏳</div>
          <div class="stat-info"><h3 id="stat-pending-claims">0</h3><p>Pending Claims</p></div>
        </div>
        <div class="stat-card" style="--stat-color:var(--success)">
          <div class="stat-icon" style="background:#d4edda;color:var(--success)">✅</div>
          <div class="stat-info"><h3 id="stat-approved-claims">0</h3><p>Approved Claims</p></div>
        </div>
        <div class="stat-card" style="--stat-color:var(--secondary)">
          <div class="stat-icon" style="background:#fff3cd;color:var(--secondary-dark)">💰</div>
          <div class="stat-info">
            <h3 id="stat-total-indemnity" style="font-size:18px">₱0</h3>
            <p>Total Indemnity</p>
          </div>
        </div>
      </div>

      <!-- Filter Tabs -->
      <div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap">
        <button class="btn btn-primary" id="tab-all"      onclick="filterClaims('')">All Claims</button>
        <button class="btn btn-ghost"   id="tab-pending"  onclick="filterClaims('Pending')">⏳ Pending</button>
        <button class="btn btn-ghost"   id="tab-approved" onclick="filterClaims('Approved')">✅ Approved</button>
        <button class="btn btn-ghost"   id="tab-rejected" onclick="filterClaims('Rejected')">❌ Rejected</button>
      </div>

      <div class="card">
        <div class="card-header"><h5>📩 Claims List</h5></div>
        <div class="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>Claim ID</th>
                <th>App ID</th>
                <th>Farmer Name</th>
                <th>Date Filed</th>
                <th>Date of Loss</th>
                <th>Cause</th>
                <th>% Damage</th>
                <th>Status</th>
                <th>Indemnity</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="claims-table-body"></tbody>
          </table>
        </div>
        <div id="empty-state" class="empty-state hidden">
          <div class="empty-icon">📩</div>
          <h4>No Claims Found</h4>
        </div>
      </div>
    </main>
  </div>

  <!-- Claim Detail Modal -->
  <div class="modal-overlay" id="claim-modal">
    <div class="modal modal-lg">
      <div class="modal-header">
        <h4>📩 Claim Details — <span id="claim-modal-id"></span></h4>
        <button class="modal-close" onclick="closeModal('claim-modal')">×</button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="claim-edit-id" />
        <div id="claim-detail-view"></div>

        <div style="background:#f8fafc;border-radius:10px;padding:18px;margin-top:16px;border:1px solid var(--border-color)">
          <div class="form-section-title">💰 Process Claim Decision</div>
          <div class="form-row form-row-2">
            <div class="form-group">
              <label class="form-label">Indemnity Amount (₱)</label>
              <input type="number" id="indemnity-amount" class="form-control"
                placeholder="Enter approved indemnity amount" />
            </div>
            <div class="form-group">
              <label class="form-label">Admin Remarks</label>
              <input type="text" id="claim-remarks" class="form-control"
                placeholder="Optional remarks..." />
            </div>
          </div>
          <div style="display:flex;gap:10px;flex-wrap:wrap">
            <button class="btn btn-success" onclick="processClaim('Approved')">✅ Approve Claim</button>
            <button class="btn btn-danger"  onclick="processClaim('Rejected')">❌ Reject Claim</button>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-ghost" onclick="closeModal('claim-modal')">Close</button>
      </div>
    </div>
  </div>

  <?php require_once '../../includes/toast.php'; ?>
  <script>
    initTopbarUser();

    var allClaims = [];
    var currentFilter = '';

    function filterClaims(status) {
      currentFilter = status;
      ['tab-all', 'tab-pending', 'tab-approved', 'tab-rejected'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.className = 'btn btn-ghost';
      });
      const activeMap = { '': 'tab-all', Pending: 'tab-pending', Approved: 'tab-approved', Rejected: 'tab-rejected' };
      const activeId = activeMap[status];
      if (activeId) {
        const el = document.getElementById(activeId);
        if (el) el.className = 'btn btn-primary';
      }
      if (!status) { renderTable(allClaims); return; }
      const s = status.toLowerCase();
      renderTable(allClaims.filter(c => {
        const cs = (c.status || '').toLowerCase();
        if (s === 'pending')  return ['pending', 'submitted', 'under_review'].includes(cs);
        if (s === 'approved') return ['approved', 'paid'].includes(cs);
        if (s === 'rejected') return cs === 'rejected';
        return true;
      }));
    }

    async function viewClaim(id) {
      showLoading();
      try {
        const res = await api('GET', `/claims/${id}`);
        hideLoading();
        if (!res.success) { showToast('Error', 'Failed to load claim.', 'error'); return; }
        const c = res.data;
        document.getElementById('claim-edit-id').value        = c.id;
        document.getElementById('claim-modal-id').textContent = c.claim_number || 'CLM-' + c.id;
        document.getElementById('indemnity-amount').value     = c.approved_amount || c.estimated_loss || '';
        document.getElementById('claim-remarks').value        = c.remarks || '';

        const docs = (c.documents || []);
        const docsHtml = docs.length
          ? `<div style="margin-top:12px">
              <div class="form-section-title">📎 Uploaded Documents</div>
              <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:8px">
                ${docs.map(d => `
                  <a href="/web-based-crop-insurance/uploads/${d.file_path}" target="_blank"
                    style="display:flex;align-items:center;gap:6px;padding:6px 12px;background:#e8eaf6;
                      border-radius:6px;font-size:12.5px;color:#1a237e;text-decoration:none">
                    📄 ${d.file_name || 'Document'}
                  </a>`).join('')}
              </div>
            </div>`
          : '<p style="font-size:12.5px;color:var(--text-muted);margin-top:8px">No documents uploaded.</p>';

        const detailView = document.getElementById('claim-detail-view');
        if (detailView) {
          detailView.innerHTML = `
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:4px">
              <div>
                <p><strong>Claim #:</strong> ${c.claim_number || 'CLM-' + c.id}</p>
                <p><strong>Policy #:</strong> ${c.policy_number || '—'}</p>
                <p><strong>Farmer:</strong> ${(c.first_name || '') + ' ' + (c.last_name || '')}</p>
                <p><strong>Email:</strong> ${c.email || '—'}</p>
                <p><strong>Farm:</strong> ${c.farm_name || '—'}</p>
                <p><strong>Incident Type:</strong> ${c.incident_type || '—'}</p>
                <p><strong>Damage %:</strong> ${c.damage_percentage ? c.damage_percentage + '%' : '—'}</p>
              </div>
              <div>
                <p><strong>Date Filed:</strong> ${formatDate(c.created_at)}</p>
                <p><strong>Incident Date:</strong> ${formatDate(c.incident_date)}</p>
                <p><strong>Estimated Loss:</strong> ${formatCurrency(c.estimated_loss)}</p>
                <p><strong>Coverage Amount:</strong> ${formatCurrency(c.coverage_amount)}</p>
                <p><strong>Approved Amount:</strong> ${formatCurrency(c.approved_amount)}</p>
                <p><strong>Status:</strong> ${getStatusBadge(c.status)}</p>
                ${c.reviewer_name ? `<p><strong>Reviewed By:</strong> ${c.reviewer_name}</p>` : ''}
              </div>
            </div>
            ${c.description ? `<div style="background:#f8fafc;border-radius:8px;padding:10px 14px;margin-top:8px;font-size:13px">
              <strong>Description:</strong> ${c.description}</div>` : ''}
            ${c.remarks ? `<div style="background:#fff3cd;border:1px solid #ffc107;border-radius:8px;
              padding:10px 14px;margin-top:8px;font-size:13px">
              <strong>📝 Remarks:</strong> ${c.remarks}</div>` : ''}
            ${docsHtml}
          `;
        }
        openModal('claim-modal');
      } catch (err) {
        hideLoading();
        console.error(err);
        showToast('Error', 'Error loading claim.', 'error');
      }
    }

    async function processClaim(action) {
      const id = document.getElementById('claim-edit-id')?.value;
      if (!id) return;
      const approvedAmount = parseFloat(document.getElementById('indemnity-amount')?.value || 0);
      const remarks        = document.getElementById('claim-remarks')?.value?.trim() || '';

      if (action === 'Approved' && approvedAmount <= 0) {
        showToast('Required', 'Please enter the indemnity amount before approving.', 'error');
        return;
      }
      if (action === 'Rejected' && !remarks) {
        showToast('Required', 'Please enter a rejection reason in the Remarks field.', 'error');
        return;
      }

      const body = { status: action.toLowerCase(), remarks };
      if (action === 'Approved') body.approved_amount = approvedAmount;

      showLoading();
      try {
        const res = await api('PUT', `/claims/${id}/status`, body);
        hideLoading();
        if (res.success) {
          showToast('Updated', `Claim ${action.toLowerCase()} successfully.`, 'success');
          closeModal('claim-modal');
          await loadClaims();
        } else {
          showToast('Error', res.message || 'Failed to update claim.', 'error');
        }
      } catch (err) {
        hideLoading();
        console.error(err);
        showToast('Error', 'Error updating claim.', 'error');
      }
    }

    async function quickAction(id, status) {
      const label = status === 'approved' ? 'Approve' : 'Reject';
      if (!confirm(`${label} this claim?`)) return;
      const claim  = allClaims.find(c => c.id == id);
      const body   = { status, remarks: `${label}d by admin.` };
      if (status === 'approved') body.approved_amount = parseFloat(claim?.estimated_loss || 0);

      showLoading();
      try {
        const res = await api('PUT', `/claims/${id}/status`, body);
        hideLoading();
        if (res.success) {
          showToast('Updated', `Claim ${status}.`, 'success');
          await loadClaims();
        } else {
          showToast('Error', res.message || `Failed to ${status} claim.`, 'error');
        }
      } catch (err) {
        hideLoading();
        console.error(err);
        showToast('Error', 'Error processing claim.', 'error');
      }
    }

    function updateStats() {
      const pending  = allClaims.filter(c => ['submitted', 'pending', 'under_review'].includes(c.status)).length;
      const approved = allClaims.filter(c => ['approved', 'paid'].includes(c.status)).length;
      const totalInd = allClaims
        .filter(c => ['approved', 'paid'].includes(c.status))
        .reduce((s, c) => s + parseFloat(c.approved_amount || 0), 0);
      const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
      set('stat-pending-claims',  pending);
      set('stat-approved-claims', approved);
      const indEl = document.getElementById('stat-total-indemnity');
      if (indEl) indEl.textContent = formatCurrency(totalInd);
    }

    function renderTable(claims) {
      const tbody = document.getElementById('claims-table-body');
      const empty = document.getElementById('empty-state');
      if (!tbody) return;
      if (!claims.length) {
        tbody.innerHTML = '<tr><td colspan="10" style="text-align:center;color:var(--text-muted);padding:20px">No claims found.</td></tr>';
        if (empty) empty.classList.remove('hidden');
        return;
      }
      if (empty) empty.classList.add('hidden');
      tbody.innerHTML = claims.map(c => `
        <tr>
          <td><strong>${c.claim_number || 'CLM-' + c.id}</strong></td>
          <td>${c.policy_number || '—'}</td>
          <td>${(c.first_name || '') + ' ' + (c.last_name || '')}</td>
          <td>${formatDate(c.created_at)}</td>
          <td>${formatDate(c.incident_date)}</td>
          <td>${c.incident_type || '—'}</td>
          <td>${c.damage_percentage ? c.damage_percentage + '%' : '—'}</td>
          <td>${getStatusBadge(c.status)}</td>
          <td>${formatCurrency(c.approved_amount || c.estimated_loss)}</td>
          <td style="white-space:nowrap">
            <button class="btn btn-sm btn-primary" onclick="viewClaim(${c.id})" style="margin-right:4px">🔍 Review</button>
            ${['submitted', 'pending', 'under_review'].includes(c.status) ? `
              <button class="btn btn-sm btn-success" onclick="quickAction(${c.id},'approved')" style="margin-right:4px" title="Quick Approve">✅</button>
              <button class="btn btn-sm btn-danger"  onclick="quickAction(${c.id},'rejected')" title="Quick Reject">❌</button>` : ''}
          </td>
        </tr>`).join('');
    }

    async function loadClaims() {
      showLoading();
      try {
        const res = await api('GET', '/claims?per_page=200');
        hideLoading();
        if (!res.success) { showToast('Error', 'Failed to load claims.', 'error'); return; }
        allClaims = res.data || [];
        updateStats();
        filterClaims(currentFilter);
      } catch (err) {
        hideLoading();
        console.error(err);
        showToast('Error', 'Error loading claims.', 'error');
      }
    }

    loadClaims();
  </script>
</body>
</html>
