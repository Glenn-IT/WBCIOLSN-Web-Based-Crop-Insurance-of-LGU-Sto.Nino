<?php require_once '../../components/under-construction.php'; ?>
<?php
$pageTitle   = 'View Applications — Admin';
$basePath    = '../../';
$currentPage = 'view-applications';
$guardRole   = 'admin';
require_once '../../includes/auth-guard.php';
require_once '../../includes/head.php';
?>
<body>
  <div class="app-layout">

    <?php require_once '../../includes/admin-sidebar.php'; ?>

    <?php
    $topbarTitle    = 'View Applications';
    $topbarSubtitle = 'Browse and review all submitted applications';
    $isAdmin        = true;
    require_once '../../includes/topbar.php';
    ?>

    <main class="main-content">
      <div class="page-header">
        <div class="page-header-left">
          <div class="breadcrumb">
            <span>Admin</span><span class="sep">›</span>
            <span class="current">View Applications</span>
          </div>
          <h2>All Applications</h2>
          <p>Complete list of all submitted crop insurance applications</p>
        </div>
      </div>

      <!-- Stats -->
      <div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px">
        <div class="stat-card" style="--stat-color:#1a237e">
          <div class="stat-icon" style="background:#e8eaf6;color:#1a237e">📋</div>
          <div class="stat-info"><h3 id="total-count">0</h3><p>Total</p></div>
        </div>
        <div class="stat-card" style="--stat-color:var(--success)">
          <div class="stat-icon" style="background:#d4edda;color:var(--success)">✅</div>
          <div class="stat-info"><h3 id="approved-count">0</h3><p>Approved</p></div>
        </div>
        <div class="stat-card" style="--stat-color:var(--warning)">
          <div class="stat-icon" style="background:#fff3cd;color:#856404">⏳</div>
          <div class="stat-info"><h3 id="pending-count">0</h3><p>Pending</p></div>
        </div>
        <div class="stat-card" style="--stat-color:var(--danger)">
          <div class="stat-icon" style="background:#f8d7da;color:var(--danger)">❌</div>
          <div class="stat-info"><h3 id="rejected-count">0</h3><p>Rejected</p></div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h5>📋 Applications List</h5>
          <div class="search-filter-bar" style="flex:1;margin-left:20px">
            <div class="search-input-wrapper">
              <span class="search-icon">🔍</span>
              <input type="text" class="form-control" id="search-input"
                placeholder="Search ID, farmer, location..." oninput="filterApps()" />
            </div>
            <select class="form-select" id="status-filter" onchange="filterApps()" style="width:160px">
              <option value="">All Status</option>
              <option value="pending">Pending</option>
              <option value="under_review">Under Review</option>
              <option value="active">Approved / Active</option>
              <option value="rejected">Rejected</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
        </div>
        <div class="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>Policy #</th>
                <th>Farmer Name</th>
                <th>Farm</th>
                <th>Coverage</th>
                <th>Premium</th>
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
          <p>No applications match your search.</p>
        </div>
      </div>
    </main>
  </div>

  <!-- View Detail Modal -->
  <div class="modal-overlay" id="view-modal">
    <div class="modal modal-xl">
      <div class="modal-header">
        <h4>📋 Full Application Details</h4>
        <button class="modal-close" onclick="closeModal('view-modal')">×</button>
      </div>
      <div class="modal-body" id="view-modal-body"></div>
      <div class="modal-footer">
        <button class="btn btn-ghost" onclick="closeModal('view-modal')">Close</button>
      </div>
    </div>
  </div>

  <?php require_once '../../includes/toast.php'; ?>
  <script>
    initTopbarUser();

    let allApps = [];

    async function loadApps() {
      showLoading();
      try {
        const res = await api('GET', '/policies?per_page=200');
        hideLoading();
        if (!res.success) {
          showToast('Error', res.message || 'Failed to load applications.', 'error');
          return;
        }
        allApps = res.data || [];
        document.getElementById('total-count').textContent    = allApps.length;
        document.getElementById('approved-count').textContent = allApps.filter(a => a.status === 'active').length;
        document.getElementById('pending-count').textContent  = allApps.filter(a => a.status === 'pending' || a.status === 'under_review').length;
        document.getElementById('rejected-count').textContent = allApps.filter(a => a.status === 'rejected').length;
        renderTable(allApps);
      } catch (err) {
        hideLoading();
        console.error(err);
        showToast('Error', 'Error loading applications.', 'error');
      }
    }

    function renderTable(apps) {
      const tbody = document.getElementById('apps-table-body');
      const empty = document.getElementById('empty-state');
      if (apps.length === 0) {
        tbody.innerHTML = '';
        empty.classList.remove('hidden');
        return;
      }
      empty.classList.add('hidden');
      tbody.innerHTML = apps.map(a => `
        <tr>
          <td><strong>${a.policy_number || a.id}</strong><br/>
            <small style="color:var(--text-muted)">${formatDate(a.start_date)}</small></td>
          <td>${a.first_name ? a.first_name + ' ' + a.last_name : 'User #' + a.user_id}<br/>
            <small style="color:var(--text-muted)">${a.plan_name || ''}</small></td>
          <td style="max-width:180px"><span style="font-size:12.5px">${a.farm_name || a.farm_location || '—'}</span></td>
          <td>${formatCurrency(a.coverage_amount)}</td>
          <td>${formatCurrency(a.total_premium)}</td>
          <td>${getStatusBadge(a.status)}</td>
          <td><button class="btn btn-sm btn-outline" onclick="viewApp(${a.id})">👁 View</button></td>
        </tr>`).join('');
    }

    function filterApps() {
      const search = document.getElementById('search-input').value.toLowerCase();
      const status = document.getElementById('status-filter').value;
      const filtered = allApps.filter(a => {
        const name = (a.first_name ? a.first_name + ' ' + a.last_name : '').toLowerCase();
        const matchSearch = !search ||
          (a.policy_number || '').toLowerCase().includes(search) ||
          name.includes(search) ||
          (a.farm_name || '').toLowerCase().includes(search) ||
          (a.farm_location || '').toLowerCase().includes(search);
        const matchStatus = !status || a.status === status;
        return matchSearch && matchStatus;
      });
      renderTable(filtered);
    }

    async function viewApp(id) {
      showLoading();
      try {
        const res = await api('GET', '/policies/' + id);
        hideLoading();
        if (!res.success) {
          showToast('Error', res.message || 'Failed to load details.', 'error');
          return;
        }
        const app = res.data;
        const remarksHtml = app.remarks
          ? `<div style="background:#fff3cd;border:1px solid #ffc107;border-radius:8px;padding:10px 14px;
              margin-top:12px;font-size:13px"><strong>📝 Remarks:</strong> ${app.remarks}</div>`
          : '';
        document.getElementById('view-modal-body').innerHTML = `
          <div style="background:linear-gradient(135deg,#e8eaf6,#f0f0ff);border-radius:10px;padding:16px;
            margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
            <div>
              <p style="font-size:12px;color:var(--text-muted);font-weight:600;text-transform:uppercase">Policy Number</p>
              <p style="font-size:20px;font-weight:800;color:#1a237e">${app.policy_number || app.id}</p>
            </div>
            <div style="text-align:right">${getStatusBadge(app.status)}<br/>
              <small style="color:var(--text-muted)">Submitted: ${formatDate(app.created_at)}</small></div>
          </div>
          ${remarksHtml}
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:16px">
            <div>
              <div class="detail-section"><div class="detail-section-title">Basic Information</div>
                <div class="detail-grid">
                  <div class="detail-item"><label>Farmer</label><p>${app.first_name ? app.first_name + ' ' + app.last_name : 'User #' + app.user_id}</p></div>
                  <div class="detail-item"><label>Email</label><p>${app.email || '—'}</p></div>
                  <div class="detail-item"><label>Plan</label><p>${app.plan_name || '—'}</p></div>
                  <div class="detail-item"><label>Coverage Type</label><p>${app.coverage_type || '—'}</p></div>
                  <div class="detail-item"><label>Start Date</label><p>${formatDate(app.start_date)}</p></div>
                  <div class="detail-item"><label>End Date</label><p>${formatDate(app.end_date)}</p></div>
                </div>
              </div>
              <div class="detail-section"><div class="detail-section-title">Farm Information</div>
                <div class="detail-grid">
                  <div class="detail-item" style="grid-column:1/-1"><label>Farm Name</label><p>${app.farm_name || '—'}</p></div>
                  <div class="detail-item"><label>Location</label><p>${app.farm_location || '—'}</p></div>
                  <div class="detail-item"><label>Area (ha)</label><p>${app.area_hectares || '—'}</p></div>
                  <div class="detail-item"><label>Crop Type</label><p>${app.crop_type || '—'}</p></div>
                </div>
              </div>
            </div>
            <div>
              <div class="detail-section"><div class="detail-section-title">Coverage & Premium</div>
                <div class="detail-grid">
                  <div class="detail-item"><label>Coverage Amount</label>
                    <p style="font-size:18px;font-weight:700;color:var(--primary)">${formatCurrency(app.coverage_amount)}</p></div>
                  <div class="detail-item"><label>Total Premium</label>
                    <p style="font-size:18px;font-weight:700;color:var(--success)">${formatCurrency(app.total_premium)}</p></div>
                  <div class="detail-item"><label>% Damage</label><p>${app.percent_damage ? app.percent_damage + '%' : '—'}</p></div>
                  <div class="detail-item"><label>Cause of Damage</label><p>${app.cause_of_damage || '—'}</p></div>
                </div>
              </div>
              <div class="detail-section"><div class="detail-section-title">Verification Status</div>
                <div style="display:flex;flex-direction:column;gap:8px">
                  ${['farm_verification','damage_verification','coverage_verification'].map(k => `
                    <div style="display:flex;justify-content:space-between;align-items:center;
                      padding:8px 12px;background:#f8fafc;border-radius:8px">
                      <span style="font-size:13px">${k.replace('_verification','').replace('_',' ').replace(/\b\w/g,c=>c.toUpperCase())} Verification</span>
                      <span class="badge ${app[k]==='Verified'?'badge-success':app[k]==='Rejected'?'badge-danger':'badge-warning'}">${app[k]||'Pending'}</span>
                    </div>`).join('')}
                  <div style="display:flex;justify-content:space-between;align-items:center;
                    padding:8px 12px;background:#f8fafc;border-radius:8px;margin-top:4px">
                    <span style="font-size:13px">Application Status</span>${getStatusBadge(app.status)}
                  </div>
                </div>
              </div>
              ${app.agent_name ? `<div class="detail-section"><div class="detail-section-title">Handled By</div>
                <div class="detail-item"><label>Agent / Admin</label><p>${app.agent_name}</p></div>
              </div>` : ''}
            </div>
          </div>`;
        openModal('view-modal');
      } catch (err) {
        hideLoading();
        console.error(err);
        showToast('Error', 'Error loading application details.', 'error');
      }
    }

    loadApps();
  </script>
</body>
</html>
