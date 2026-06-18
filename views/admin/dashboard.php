<?php require_once '../../components/under-construction.php'; ?>
<?php
$pageTitle      = 'Admin Dashboard — LGU Crop Insurance';
$basePath       = '../../';
$includeChartJs = true;
$currentPage    = 'dashboard';
$guardRole      = 'admin';
require_once '../../includes/auth-guard.php';
require_once '../../includes/head.php';
?>
<body>
  <div class="app-layout">

    <?php require_once '../../includes/admin-sidebar.php'; ?>

    <?php
    $topbarTitle    = 'Admin Dashboard';
    $topbarSubtitle = 'System Overview';
    $isAdmin        = true;
    require_once '../../includes/topbar.php';
    ?>

    <main class="main-content">
      <!-- Welcome Banner -->
      <div style="background:linear-gradient(135deg,#1a237e,#283593);border-radius:16px;
        padding:28px 32px;margin-bottom:28px;color:white;
        display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px">
        <div>
          <h2 style="font-size:22px;font-weight:800;margin-bottom:6px">
            Welcome, <?= htmlspecialchars($authUser['first_name'] ?? 'Administrator') ?>! 🛡️
          </h2>
          <p style="opacity:0.85;font-size:14px">
            LGU Sto. Niño Agricultural Insurance Management System
          </p>
          <div style="display:flex;gap:10px;margin-top:16px;flex-wrap:wrap">
            <button class="btn btn-secondary" onclick="navigateTo('manage-applications.php')">
              ⚙️ Manage Applications
            </button>
            <button class="btn"
              style="background:rgba(255,255,255,0.15);color:white;border:1px solid rgba(255,255,255,0.3)"
              onclick="navigateTo('reports.php')">
              📈 View Reports
            </button>
          </div>
        </div>
        <div style="font-size:80px;opacity:0.3">🏛️</div>
      </div>

      <!-- Stats Grid -->
      <div class="stats-grid" style="grid-template-columns:repeat(5,1fr)">
        <div class="stat-card" style="--stat-color:#1a237e">
          <div class="stat-icon" style="background:#e8eaf6;color:#1a237e">📋</div>
          <div class="stat-info"><h3 id="stat-total">0</h3><p>Total Applications</p></div>
        </div>
        <div class="stat-card" style="--stat-color:var(--success)">
          <div class="stat-icon" style="background:#d4edda;color:var(--success)">✅</div>
          <div class="stat-info"><h3 id="stat-approved">0</h3><p>Approved</p></div>
        </div>
        <div class="stat-card" style="--stat-color:var(--warning)">
          <div class="stat-icon" style="background:#fff3cd;color:#856404">⏳</div>
          <div class="stat-info"><h3 id="stat-pending">0</h3><p>Pending</p></div>
        </div>
        <div class="stat-card" style="--stat-color:var(--danger)">
          <div class="stat-icon" style="background:#f8d7da;color:var(--danger)">❌</div>
          <div class="stat-info"><h3 id="stat-rejected">0</h3><p>Rejected</p></div>
        </div>
        <div class="stat-card" style="--stat-color:var(--secondary)">
          <div class="stat-icon" style="background:#fff3cd;color:var(--secondary-dark)">💰</div>
          <div class="stat-info">
            <h3 id="stat-indemnity" style="font-size:18px">₱0</h3>
            <p>Total Indemnity</p>
          </div>
        </div>
      </div>

      <!-- Charts Row 1 -->
      <div class="grid-2" style="margin-bottom:24px">
        <div class="card">
          <div class="card-header"><h5>📈 Monthly Applications vs Claims</h5></div>
          <div class="card-body">
            <div class="chart-container"><canvas id="monthlyChart"></canvas></div>
          </div>
        </div>
        <div class="card">
          <div class="card-header"><h5>🥧 Application Status Distribution</h5></div>
          <div class="card-body">
            <div class="chart-container"><canvas id="statusChart"></canvas></div>
          </div>
        </div>
      </div>

      <!-- Charts Row 2 -->
      <div class="grid-2" style="margin-bottom:24px">
        <div class="card">
          <div class="card-header"><h5>🌪️ Damage Type Analysis</h5></div>
          <div class="card-body">
            <div class="chart-container chart-container-sm"><canvas id="damageChart"></canvas></div>
          </div>
        </div>
        <div class="card">
          <div class="card-header"><h5>💰 Monthly Indemnity Released (₱)</h5></div>
          <div class="card-body">
            <div class="chart-container chart-container-sm"><canvas id="indemnityChart"></canvas></div>
          </div>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="grid-2">
        <div class="card">
          <div class="card-header">
            <h5>🕐 Recent Applications</h5>
            <button class="btn btn-sm btn-outline" onclick="navigateTo('view-applications.php')">View All</button>
          </div>
          <div style="overflow-x:auto">
            <table>
              <thead>
                <tr>
                  <th>ID</th><th>Farmer</th><th>Type</th><th>Status</th>
                </tr>
              </thead>
              <tbody id="recent-apps-table"></tbody>
            </table>
          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <h5>📩 Recent Claims</h5>
            <button class="btn btn-sm btn-outline" onclick="navigateTo('claim-verification.php')">View All</button>
          </div>
          <div style="overflow-x:auto">
            <table>
              <thead>
                <tr>
                  <th>Claim ID</th><th>App ID</th><th>Cause</th><th>Status</th>
                </tr>
              </thead>
              <tbody id="recent-claims-table"></tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>

  <?php require_once '../../includes/toast.php'; ?>
  <script>
    initTopbarUser();
    loadAdminDashboard();

    async function loadAdminDashboard() {
      try {
        // 1. Stats + status doughnut
        const statsRes = await api('GET', '/dashboard/stats');
        if (statsRes.success) {
          const pol = statsRes.data.policies || {};
          const clm = statsRes.data.claims   || {};
          const set = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v ?? 0; };
          set('stat-total',    pol.total    ?? 0);
          set('stat-pending',  pol.pending  ?? 0);
          set('stat-approved', pol.active   ?? 0);
          set('stat-rejected', pol.rejected ?? 0);
          const indEl = document.getElementById('stat-indemnity');
          if (indEl) indEl.textContent = formatCurrency(clm.total_approved_amount ?? 0);

          const pieEl = document.getElementById('statusChart');
          if (pieEl) {
            new Chart(pieEl, {
              type: 'doughnut',
              data: {
                labels: ['Active/Approved', 'Pending', 'Rejected', 'Cancelled'],
                datasets: [{
                  data: [pol.active || 0, pol.pending || 0, pol.rejected || 0, pol.cancelled || 0],
                  backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#6c757d'],
                }],
              },
              options: { responsive: true, plugins: { legend: { position: 'bottom' } } },
            });
          }
        }

        // 2. Recent applications table (latest 5)
        const appsRes = await api('GET', '/policies?limit=5');
        const appsTbody = document.getElementById('recent-apps-table');
        if (appsRes.success && appsTbody) {
          const apps = appsRes.data || [];
          appsTbody.innerHTML = apps.length
            ? apps.map(p => `<tr>
                <td>${p.policy_number || 'APP-' + p.id}</td>
                <td>${(p.first_name || '') + ' ' + (p.last_name || '')}</td>
                <td>${p.plan_name || '—'}</td>
                <td>${getStatusBadge(p.status)}</td>
              </tr>`).join('')
            : '<tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:16px">No applications yet.</td></tr>';
        }

        // 3. Recent claims table (latest 5)
        const claimsRes = await api('GET', '/claims?limit=5');
        const claimsTbody = document.getElementById('recent-claims-table');
        if (claimsRes.success && claimsTbody) {
          const claims = claimsRes.data || [];
          claimsTbody.innerHTML = claims.length
            ? claims.map(c => `<tr>
                <td>${c.claim_number || 'CLM-' + c.id}</td>
                <td>${c.policy_number || '—'}</td>
                <td>${c.incident_type || '—'}</td>
                <td>${getStatusBadge(c.status)}</td>
              </tr>`).join('')
            : '<tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:16px">No claims yet.</td></tr>';
        }

        // 4. Monthly Applications vs Claims chart — from report record sets
        const [polRpt, clmRpt] = await Promise.all([
          api('GET', '/reports/policies'),
          api('GET', '/reports/claims'),
        ]);
        const polRecords = polRpt.success ? (polRpt.data?.records || []) : [];
        const clmRecords = clmRpt.success ? (clmRpt.data?.records || []) : [];

        const monthApps   = {};
        const monthClaims = {};
        polRecords.forEach(p => { const m = (p.created_at || '').slice(0, 7); if (m) monthApps[m]   = (monthApps[m]   || 0) + 1; });
        clmRecords.forEach(c => { const m = (c.created_at || '').slice(0, 7); if (m) monthClaims[m] = (monthClaims[m] || 0) + 1; });

        const allMonths = Array.from(new Set([...Object.keys(monthApps), ...Object.keys(monthClaims)])).sort().slice(-6);
        const chartEl   = document.getElementById('monthlyChart');
        if (chartEl) {
          new Chart(chartEl, {
            type: 'bar',
            data: {
              labels: allMonths.length ? allMonths : ['No data'],
              datasets: [
                { label: 'Applications', data: allMonths.map(m => monthApps[m]   || 0), backgroundColor: 'rgba(26,35,126,0.7)' },
                { label: 'Claims',       data: allMonths.map(m => monthClaims[m] || 0), backgroundColor: 'rgba(220,53,69,0.7)'  },
              ],
            },
            options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } },
          });
        }

        // 5. Claims by crop type → damageChart
        const byCropRes = await api('GET', '/reports/claims/by-crop');
        if (byCropRes.success) {
          const cropData = byCropRes.data || [];
          const dmgEl   = document.getElementById('damageChart');
          if (dmgEl && cropData.length) {
            new Chart(dmgEl, {
              type: 'bar',
              data: {
                labels: cropData.map(r => r.crop_type || '—'),
                datasets: [{
                  label: 'Claims',
                  data: cropData.map(r => parseInt(r.total_claims) || 0),
                  backgroundColor: ['#007bff','#28a745','#ffc107','#dc3545','#6c757d','#17a2b8'],
                }],
              },
              options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } },
            });
          }
        }

        // 6. Monthly premium trend → indemnityChart
        const trendRes  = await api('GET', '/reports/trends/premium');
        if (trendRes.success) {
          const trendData = trendRes.data || [];
          const indEl     = document.getElementById('indemnityChart');
          if (indEl) {
            new Chart(indEl, {
              type: 'line',
              data: {
                labels: trendData.length ? trendData.map(r => r.month) : ['No data'],
                datasets: [{
                  label: 'Premium Collected (₱)',
                  data: trendData.map(r => parseFloat(r.total || 0)),
                  borderColor: '#28a745',
                  backgroundColor: 'rgba(40,167,69,0.1)',
                  fill: true,
                  tension: 0.4,
                }],
              },
              options: { responsive: true, scales: { y: { beginAtZero: true } } },
            });
          }
        }

      } catch (err) {
        console.error('Admin dashboard error:', err);
        showToast('Error', 'Failed to load dashboard data.', 'error');
      }
    }
  </script>
</body>
</html>
