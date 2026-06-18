<?php require_once '../../components/under-construction.php'; ?>
<?php
$pageTitle      = 'Reports — Admin';
$basePath       = '../../';
$currentPage    = 'reports';
$guardRole      = 'admin';
$includeChartJs = true;
require_once '../../includes/auth-guard.php';
require_once '../../includes/head.php';
?>
<style>
  @media print {
    .sidebar, .topbar, .page-header, .print-controls, .breadcrumb { display: none !important; }
    .main-content { margin: 0 !important; padding: 16px !important; }
    .card { break-inside: avoid; }
  }
</style>
<body>
  <div class="app-layout">

    <?php require_once '../../includes/admin-sidebar.php'; ?>

    <?php
    $topbarTitle    = 'Reports';
    $topbarSubtitle = 'System analytics and printable summaries';
    $isAdmin        = true;
    require_once '../../includes/topbar.php';
    ?>

    <main class="main-content">
      <div class="page-header">
        <div class="page-header-left">
          <div class="breadcrumb">
            <span>Admin</span><span class="sep">›</span>
            <span class="current">Reports</span>
          </div>
          <h2>System Reports</h2>
          <p>Analytics, summaries, and printable records</p>
        </div>
        <div class="page-header-right print-controls" style="display:flex;gap:8px;flex-wrap:wrap">
          <button class="btn btn-outline" onclick="exportReport('policies')">⬇️ Export Applications</button>
          <button class="btn btn-outline" onclick="exportReport('claims')">⬇️ Export Claims</button>
          <button class="btn btn-primary" onclick="window.print()">🖨️ Print Report</button>
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="stats-grid" style="margin-bottom:24px">
        <div class="stat-card" style="--stat-color:var(--primary)">
          <div class="stat-icon" style="background:#e8eaf6;color:var(--primary)">📄</div>
          <div class="stat-info"><h3 id="r-total">0</h3><p>Total Applications</p></div>
        </div>
        <div class="stat-card" style="--stat-color:var(--success)">
          <div class="stat-icon" style="background:#d4edda;color:var(--success)">✅</div>
          <div class="stat-info"><h3 id="r-approved">0</h3><p>Approved</p></div>
        </div>
        <div class="stat-card" style="--stat-color:var(--warning)">
          <div class="stat-icon" style="background:#fff3cd;color:#856404">⏳</div>
          <div class="stat-info"><h3 id="r-pending">0</h3><p>Pending</p></div>
        </div>
        <div class="stat-card" style="--stat-color:var(--secondary)">
          <div class="stat-icon" style="background:#fff3cd;color:var(--secondary-dark)">💰</div>
          <div class="stat-info">
            <h3 id="r-indemnity" style="font-size:18px">₱0</h3>
            <p>Total Indemnity</p>
          </div>
        </div>
      </div>

      <!-- Charts Row -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px">
        <div class="card">
          <div class="card-header"><h5>📊 Applications by Status</h5></div>
          <div style="padding:16px;height:240px;display:flex;align-items:center;justify-content:center">
            <canvas id="statusChart"></canvas>
          </div>
        </div>
        <div class="card">
          <div class="card-header"><h5>🌧️ Claims by Cause</h5></div>
          <div style="padding:16px;height:240px;display:flex;align-items:center;justify-content:center">
            <canvas id="causeChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Applications Table -->
      <div class="card" style="margin-bottom:24px">
        <div class="card-header" style="justify-content:space-between">
          <h5>📋 Applications Summary</h5>
          <span id="app-count" style="font-size:13px;color:var(--text-muted)"></span>
        </div>
        <div class="table-wrapper">
          <table id="report-app-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Policy #</th>
                <th>Farmer Name</th>
                <th>Farm</th>
                <th>Plan</th>
                <th>Area (ha)</th>
                <th>Coverage</th>
                <th>Status</th>
                <th>Date Submitted</th>
              </tr>
            </thead>
            <tbody id="app-table-body"></tbody>
            <tfoot id="app-table-foot"></tfoot>
          </table>
        </div>
      </div>

      <!-- Claims Table -->
      <div class="card">
        <div class="card-header" style="justify-content:space-between">
          <h5>📩 Claims Summary</h5>
          <span id="claims-count" style="font-size:13px;color:var(--text-muted)"></span>
        </div>
        <div class="table-wrapper">
          <table id="report-claim-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Claim #</th>
                <th>Policy #</th>
                <th>Farmer Name</th>
                <th>Farm</th>
                <th>Date Filed</th>
                <th>Cause</th>
                <th>Indemnity</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="claims-table-body"></tbody>
            <tfoot id="claims-table-foot"></tfoot>
          </table>
        </div>
      </div>
    </main>
  </div>

  <?php require_once '../../includes/toast.php'; ?>
  <script>
    initTopbarUser();

    async function exportReport(type) {
      showLoading();
      try {
        const token = getToken();
        const resp  = await fetch(`${API_BASE}/reports/export/${type}`, {
          headers: { Authorization: `Bearer ${token}` },
        });
        hideLoading();
        if (!resp.ok) {
          const err = await resp.json().catch(() => ({}));
          showToast('Export Failed', err.message || `Failed to export ${type}.`, 'error');
          return;
        }
        const blob = await resp.blob();
        const url  = URL.createObjectURL(blob);
        const a    = document.createElement('a');
        a.href     = url;
        a.download = `lgu-crop-insurance-${type}-${new Date().toISOString().slice(0, 10)}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        showToast('Exported', `${type.charAt(0).toUpperCase() + type.slice(1)} CSV downloaded.`, 'success');
      } catch (err) {
        hideLoading();
        console.error(err);
        showToast('Export Error', `Error exporting ${type}.`, 'error');
      }
    }

    async function loadReports() {
      showLoading();
      try {
        // 1. Stat cards + status doughnut from dashboard/stats
        const statsRes = await api('GET', '/dashboard/stats');
        if (statsRes.success) {
          const pol = statsRes.data.policies || {};
          const clm = statsRes.data.claims   || {};
          const set = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v ?? 0; };
          set('r-total',    pol.total    ?? 0);
          set('r-approved', pol.active   ?? 0);
          set('r-pending',  pol.pending  ?? 0);
          const indEl = document.getElementById('r-indemnity');
          if (indEl) indEl.textContent = formatCurrency(clm.total_approved_amount ?? 0);

          const statusEl = document.getElementById('statusChart');
          if (statusEl) {
            new Chart(statusEl, {
              type: 'doughnut',
              data: {
                labels: ['Active/Approved', 'Pending', 'Rejected', 'Cancelled'],
                datasets: [{
                  data: [pol.active || 0, pol.pending || 0, pol.rejected || 0, pol.cancelled || 0],
                  backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#6c757d'],
                }],
              },
              options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } },
            });
          }
        }

        // 2. Applications table from /reports/policies
        // Returns records with: policy_number, farmer_name, farm_name, area_hectares,
        //                        plan_name, coverage_amount, status, created_at
        const polRpt = await api('GET', '/reports/policies');
        if (polRpt.success) {
          const records = polRpt.data?.records || [];
          const countEl = document.getElementById('app-count');
          if (countEl) countEl.textContent = records.length + ' records';
          const tbody = document.getElementById('app-table-body');
          if (tbody) {
            tbody.innerHTML = records.length
              ? records.map((p, i) => `
                  <tr>
                    <td>${i + 1}</td>
                    <td><strong>${p.policy_number || '—'}</strong></td>
                    <td>${p.farmer_name || '—'}</td>
                    <td>${p.farm_name || '—'}</td>
                    <td>${p.plan_name || '—'}</td>
                    <td>${p.area_hectares || '—'}</td>
                    <td>${formatCurrency(p.coverage_amount)}</td>
                    <td>${getStatusBadge(p.status)}</td>
                    <td>${formatDate(p.created_at)}</td>
                  </tr>`).join('')
              : '<tr><td colspan="9" style="text-align:center;color:var(--text-muted);padding:16px">No applications.</td></tr>';
          }
          const foot = document.getElementById('app-table-foot');
          if (foot && records.length) {
            const totalCoverage = records.reduce((s, p) => s + parseFloat(p.coverage_amount || 0), 0);
            foot.innerHTML = `<tr style="font-weight:700;background:#f8fafc">
              <td colspan="6">Totals</td>
              <td>${formatCurrency(totalCoverage)}</td>
              <td></td><td></td>
            </tr>`;
          }
        }

        // 3. Claims table + cause chart from /reports/claims
        // Returns records with: claim_number, policy_number, farmer_name, farm_name, location,
        //                        incident_type, incident_date, estimated_loss, approved_amount,
        //                        status, created_at
        const clmRpt = await api('GET', '/reports/claims');
        if (clmRpt.success) {
          const records = clmRpt.data?.records || [];
          const totalIndemnity = records
            .filter(c => ['approved', 'paid'].includes(c.status))
            .reduce((s, c) => s + parseFloat(c.approved_amount || 0), 0);

          const claimsCount = document.getElementById('claims-count');
          if (claimsCount) claimsCount.textContent = records.length + ' records';

          const claimsTbody = document.getElementById('claims-table-body');
          if (claimsTbody) {
            claimsTbody.innerHTML = records.length
              ? records.map((c, i) => `
                  <tr>
                    <td>${i + 1}</td>
                    <td><strong>${c.claim_number || 'CLM-' + c.id}</strong></td>
                    <td>${c.policy_number || '—'}</td>
                    <td>${c.farmer_name || '—'}</td>
                    <td>${c.farm_name || '—'}</td>
                    <td>${formatDate(c.created_at)}</td>
                    <td>${c.incident_type || '—'}</td>
                    <td>${formatCurrency(c.approved_amount || c.estimated_loss)}</td>
                    <td>${getStatusBadge(c.status)}</td>
                  </tr>`).join('')
              : '<tr><td colspan="9" style="text-align:center;color:var(--text-muted);padding:16px">No claims.</td></tr>';
          }

          const claimsFoot = document.getElementById('claims-table-foot');
          if (claimsFoot && records.length) {
            claimsFoot.innerHTML = `<tr style="font-weight:700;background:#f8fafc">
              <td colspan="7">Total Indemnity</td>
              <td>${formatCurrency(totalIndemnity)}</td>
              <td></td>
            </tr>`;
          }

          // Claims by cause bar chart
          const causeCounts = {};
          records.forEach(c => {
            const k = c.incident_type || 'Unknown';
            causeCounts[k] = (causeCounts[k] || 0) + 1;
          });
          const causeEl = document.getElementById('causeChart');
          if (causeEl) {
            const labels = Object.keys(causeCounts);
            new Chart(causeEl, {
              type: 'bar',
              data: {
                labels: labels.length ? labels : ['No data'],
                datasets: [{
                  label: 'Claims',
                  data: labels.length ? Object.values(causeCounts) : [0],
                  backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d', '#17a2b8'],
                }],
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
              },
            });
          }
        }

        hideLoading();
      } catch (err) {
        hideLoading();
        console.error('Reports error:', err);
        showToast('Error', 'Failed to load reports.', 'error');
      }
    }

    loadReports();
  </script>
</body>
</html>
