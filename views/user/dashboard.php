<?php require_once '../../components/under-construction.php'; ?>
<?php
$pageTitle      = 'Dashboard — Farmer Portal';
$basePath       = '../../';
$includeChartJs = true;
$currentPage    = 'dashboard';
$guardRole      = 'user';
require_once '../../includes/auth-guard.php';
require_once '../../includes/head.php';
?>
<body>
  <div class="app-layout">

    <?php require_once '../../includes/user-sidebar.php'; ?>

    <?php
    $topbarTitle    = 'Dashboard';
    $topbarSubtitle = "Welcome back! Here's your overview.";
    $isAdmin        = false;
    require_once '../../includes/topbar.php';
    ?>

    <!-- Main Content -->
    <main class="main-content">

      <!-- Welcome Banner -->
      <div style="background:linear-gradient(135deg,var(--primary-dark),var(--primary-light));
        border-radius:16px;padding:28px 32px;margin-bottom:28px;color:white;
        display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;">
        <div>
          <h2 style="font-size:22px;font-weight:800;margin-bottom:6px">
            Good day, <span id="welcome-name"><?= htmlspecialchars($authUser['first_name'] ?? 'Farmer') ?></span>! 👋
          </h2>
          <p style="opacity:0.85;font-size:14px">
            Manage your crop insurance applications and claims from your dashboard.
          </p>
          <div style="display:flex;gap:10px;margin-top:16px;flex-wrap:wrap">
            <button class="btn btn-secondary" onclick="navigateTo('new-application.php')">
              📝 New Application
            </button>
            <button class="btn"
              style="background:rgba(255,255,255,0.15);color:white;border:1px solid rgba(255,255,255,0.3)"
              onclick="navigateTo('file-claim.php')">
              📩 File Claim
            </button>
          </div>
        </div>
        <div style="font-size:80px;opacity:0.3">🌾</div>
      </div>

      <!-- Stats Cards -->
      <div class="stats-grid" style="grid-template-columns:repeat(4,1fr)">
        <div class="stat-card" style="--stat-color:#1a6b3c">
          <div class="stat-icon" style="background:#e8f5e9;color:#1a6b3c">📋</div>
          <div class="stat-info">
            <h3 id="stat-apps">0</h3>
            <p>My Applications</p>
            <div class="stat-trend trend-up">↑ Active</div>
          </div>
        </div>
        <div class="stat-card" style="--stat-color:#28a745">
          <div class="stat-icon" style="background:#d4edda;color:#28a745">✅</div>
          <div class="stat-info">
            <h3 id="stat-approved">0</h3>
            <p>Approved</p>
            <div class="stat-trend trend-up">↑ Covered</div>
          </div>
        </div>
        <div class="stat-card" style="--stat-color:#ffc107">
          <div class="stat-icon" style="background:#fff3cd;color:#856404">⏳</div>
          <div class="stat-info">
            <h3 id="stat-pending">0</h3>
            <p>Pending Review</p>
            <div class="stat-trend" style="color:var(--warning)">Processing</div>
          </div>
        </div>
        <div class="stat-card" style="--stat-color:#17a2b8">
          <div class="stat-icon" style="background:#d1ecf1;color:#0c5460">📩</div>
          <div class="stat-info">
            <h3 id="stat-claims">0</h3>
            <p>Claims Filed</p>
            <div class="stat-trend trend-up">↑ Total</div>
          </div>
        </div>
      </div>

      <!-- Charts Row -->
      <div class="grid-2" style="margin-bottom:24px">
        <div class="card">
          <div class="card-header"><h5>📈 Application Status Overview</h5></div>
          <div class="card-body">
            <div class="chart-container"><canvas id="statusChart"></canvas></div>
          </div>
        </div>
        <div class="card">
          <div class="card-header"><h5>🌪️ Damage Causes Distribution</h5></div>
          <div class="card-body">
            <div class="chart-container"><canvas id="damageChart"></canvas></div>
          </div>
        </div>
      </div>

      <!-- Community Stats + Recent Applications -->
      <div class="grid-2" style="margin-bottom:24px">
        <div class="card">
          <div class="card-header"><h5>📊 My Insurance Summary</h5></div>
          <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
              <div style="background:#f8fafc;border-radius:10px;padding:16px;text-align:center;border:1px solid var(--border-color)">
                <div id="sum-farms" style="font-size:26px;font-weight:800;color:var(--primary)">—</div>
                <div style="font-size:12px;color:var(--text-muted);font-weight:600">Registered Farms</div>
              </div>
              <div style="background:#f8fafc;border-radius:10px;padding:16px;text-align:center;border:1px solid var(--border-color)">
                <div id="sum-active" style="font-size:26px;font-weight:800;color:#28a745">—</div>
                <div style="font-size:12px;color:var(--text-muted);font-weight:600">Active Coverage</div>
              </div>
              <div style="background:#f8fafc;border-radius:10px;padding:16px;text-align:center;border:1px solid var(--border-color)">
                <div id="sum-payout" style="font-size:18px;font-weight:800;color:#17a2b8">₱0</div>
                <div style="font-size:12px;color:var(--text-muted);font-weight:600">Total Payout Received</div>
              </div>
              <div style="background:#f8fafc;border-radius:10px;padding:16px;text-align:center;border:1px solid var(--border-color)">
                <div id="sum-claims-approved" style="font-size:26px;font-weight:800;color:#dc3545">—</div>
                <div style="font-size:12px;color:var(--text-muted);font-weight:600">Claims Approved</div>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h5>📋 Recent Applications</h5>
            <button class="btn btn-sm btn-outline" onclick="navigateTo('my-applications.php')">View All</button>
          </div>
          <div class="card-body" style="padding:0">
            <div id="recent-apps-list">
              <div style="padding:20px;text-align:center;color:var(--text-muted)">Loading...</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="card">
        <div class="card-header"><h5>⚡ Quick Actions</h5></div>
        <div class="card-body">
          <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px">
            <div onclick="navigateTo('new-application.php')"
              style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);border-radius:12px;
                padding:20px;text-align:center;cursor:pointer;transition:all 0.2s;border:1px solid #a5d6a7"
              onmouseover="this.style.transform='translateY(-2px)'"
              onmouseout="this.style.transform='none'">
              <div style="font-size:32px;margin-bottom:8px">📝</div>
              <p style="font-weight:700;font-size:13px;color:var(--primary)">New Application</p>
              <p style="font-size:11.5px;color:var(--text-muted)">Apply for coverage</p>
            </div>
            <div onclick="navigateTo('file-claim.php')"
              style="background:linear-gradient(135deg,#fff3cd,#ffecb3);border-radius:12px;
                padding:20px;text-align:center;cursor:pointer;transition:all 0.2s;border:1px solid #ffe082"
              onmouseover="this.style.transform='translateY(-2px)'"
              onmouseout="this.style.transform='none'">
              <div style="font-size:32px;margin-bottom:8px">📩</div>
              <p style="font-weight:700;font-size:13px;color:#856404">File a Claim</p>
              <p style="font-size:11.5px;color:var(--text-muted)">Report damage</p>
            </div>
            <div onclick="navigateTo('application-status.php')"
              style="background:linear-gradient(135deg,#d1ecf1,#bee5eb);border-radius:12px;
                padding:20px;text-align:center;cursor:pointer;transition:all 0.2s;border:1px solid #81d4fa"
              onmouseover="this.style.transform='translateY(-2px)'"
              onmouseout="this.style.transform='none'">
              <div style="font-size:32px;margin-bottom:8px">🔍</div>
              <p style="font-weight:700;font-size:13px;color:#0c5460">Check Status</p>
              <p style="font-size:11.5px;color:var(--text-muted)">Track applications</p>
            </div>
            <div onclick="navigateTo('profile.php')"
              style="background:linear-gradient(135deg,#f8d7da,#f5c6cb);border-radius:12px;
                padding:20px;text-align:center;cursor:pointer;transition:all 0.2s;border:1px solid #f1aeb5"
              onmouseover="this.style.transform='translateY(-2px)'"
              onmouseout="this.style.transform='none'">
              <div style="font-size:32px;margin-bottom:8px">👤</div>
              <p style="font-weight:700;font-size:13px;color:#721c24">My Profile</p>
              <p style="font-size:11.5px;color:var(--text-muted)">Update details</p>
            </div>
          </div>
        </div>
      </div>

    </main>
  </div>

  <?php require_once '../../includes/toast.php'; ?>
  <script>
    initTopbarUser();

    let statusChartInstance = null;
    let damageChartInstance = null;

    async function loadDashboard() {
      try {
        const [statsRes, polRes, claimRes] = await Promise.all([
          api('GET', '/dashboard/stats'),
          api('GET', '/policies'),
          api('GET', '/claims'),
        ]);

        // ── Stat cards ──────────────────────────────────────
        if (statsRes.success) {
          const d = statsRes.data;
          const totalPolicies    = d.policies?.total    ?? 0;
          const activePolicies   = d.policies?.active   ?? 0;
          const pendingPolicies  = d.policies?.pending  ?? 0;
          const rejectedPolicies = d.policies?.rejected ?? 0;
          const totalClaims      = d.claims?.total      ?? 0;

          animateCounter(document.getElementById('stat-apps'),    totalPolicies);
          animateCounter(document.getElementById('stat-approved'), activePolicies);
          animateCounter(document.getElementById('stat-pending'),  pendingPolicies);
          animateCounter(document.getElementById('stat-claims'),   totalClaims);

          // ── Insurance Summary card ──────────────────────
          const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
          set('sum-farms',           d.farms ?? 0);
          set('sum-active',          activePolicies);
          set('sum-claims-approved', d.claims?.approved ?? 0);
          const payoutEl = document.getElementById('sum-payout');
          if (payoutEl) payoutEl.textContent = formatCurrency(d.payments?.total_payout_received ?? 0);

          // ── Status doughnut chart ───────────────────────
          const statusCtx = document.getElementById('statusChart');
          if (statusCtx) {
            if (statusChartInstance) statusChartInstance.destroy();
            statusChartInstance = new Chart(statusCtx, {
              type: 'doughnut',
              data: {
                labels: ['Active / Approved', 'Pending', 'Rejected'],
                datasets: [{
                  data: [activePolicies, pendingPolicies, rejectedPolicies],
                  backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                }],
              },
              options: { responsive: true, plugins: { legend: { position: 'bottom' } } },
            });
          }
        }

        // ── Recent Applications table ────────────────────
        const list = document.getElementById('recent-apps-list');
        if (polRes.success && list) {
          const policies = (polRes.data || []).slice(0, 5);
          if (policies.length === 0) {
            list.innerHTML = `
              <div style="padding:24px;text-align:center;color:var(--text-muted)">
                <div style="font-size:32px;margin-bottom:8px">📋</div>
                <p>No applications yet.</p>
                <button class="btn btn-primary btn-sm" onclick="navigateTo('new-application.php')">Apply Now</button>
              </div>`;
          } else {
            list.innerHTML = `
              <table style="width:100%;border-collapse:collapse;font-size:13px">
                <thead>
                  <tr style="background:#f8fafc;border-bottom:1px solid var(--border-color)">
                    <th style="padding:10px 16px;text-align:left;color:var(--text-muted);font-weight:600">Policy #</th>
                    <th style="padding:10px 16px;text-align:left;color:var(--text-muted);font-weight:600">Coverage</th>
                    <th style="padding:10px 16px;text-align:left;color:var(--text-muted);font-weight:600">Status</th>
                    <th style="padding:10px 16px;text-align:left;color:var(--text-muted);font-weight:600">Date</th>
                  </tr>
                </thead>
                <tbody>
                  ${policies.map(p => `
                    <tr style="border-bottom:1px solid var(--border-color)">
                      <td style="padding:10px 16px;font-weight:600">${p.policy_number || 'APP-' + p.id}</td>
                      <td style="padding:10px 16px">${formatCurrency(p.coverage_amount || 0)}</td>
                      <td style="padding:10px 16px">${getStatusBadge(p.status)}</td>
                      <td style="padding:10px 16px;color:var(--text-muted)">${formatDate(p.created_at)}</td>
                    </tr>`).join('')}
                </tbody>
              </table>`;
          }
        }

        // ── Damage causes bar chart (from claims) ────────
        const damageCtx = document.getElementById('damageChart');
        if (damageCtx) {
          const claims = claimRes.success ? (claimRes.data || []) : [];
          const damageCounts = {};
          claims.forEach(c => {
            const cause = c.incident_type || 'Not Specified';
            damageCounts[cause] = (damageCounts[cause] || 0) + 1;
          });
          if (Object.keys(damageCounts).length > 0) {
            if (damageChartInstance) damageChartInstance.destroy();
            damageChartInstance = new Chart(damageCtx, {
              type: 'bar',
              data: {
                labels: Object.keys(damageCounts),
                datasets: [{
                  label: 'Claims',
                  data: Object.values(damageCounts),
                  backgroundColor: '#1a6b3c',
                  borderRadius: 6,
                }],
              },
              options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
              },
            });
          } else {
            damageCtx.parentElement.innerHTML =
              '<div style="display:flex;align-items:center;justify-content:center;height:160px;color:var(--text-muted);font-size:13px">No claims data yet</div>';
          }
        }
      } catch (err) {
        console.error('Dashboard load error:', err);
        showToast('Warning', 'Some dashboard data could not be loaded.', 'warning');
      }
    }

    loadDashboard();
  </script>
</body>
</html>
