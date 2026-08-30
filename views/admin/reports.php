<?php
$pageTitle      = 'Reports — Admin';
$basePath       = '../../';
$currentPage    = 'reports';
$guardRole      = 'admin';
$includeChartJs = false;
require_once '../../includes/auth-guard.php';
require_once '../../includes/head.php';
?>
<style>
  .report-container {
    background: #fff;
    border-radius: var(--border-radius);
    padding: 32px 36px;
    box-shadow: var(--card-shadow);
    margin-bottom: 24px;
    border: 1px solid var(--border-color);
  }

  .official-report-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 8px;
    gap: 20px;
  }

  .official-logo {
    width: 90px;
    height: 90px;
    object-fit: contain;
    flex-shrink: 0;
  }

  .official-text {
    text-align: center;
    flex-grow: 1;
    line-height: 1.35;
  }

  .official-text .line {
    font-size: 15px;
    font-weight: 700;
    color: #111;
    margin: 0;
  }

  .official-text .report-title {
    font-size: 17px;
    font-weight: 800;
    color: #111;
    margin-top: 14px;
    margin-bottom: 0;
    letter-spacing: 0.3px;
  }

  .official-divider {
    border: 0;
    border-top: 2px solid #222;
    margin: 14px 0 18px 0;
  }

  .report-meta {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: var(--text-muted);
    margin-bottom: 20px;
    padding-bottom: 8px;
    border-bottom: 1px dashed var(--border-color);
  }

  .section-heading {
    font-size: 15px;
    font-weight: 700;
    color: var(--primary);
    margin: 20px 0 10px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .report-table-wrapper {
    margin-bottom: 24px;
    overflow-x: auto;
  }

  .report-table-wrapper table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
  }

  .report-table-wrapper th,
  .report-table-wrapper td {
    padding: 9px 12px;
    border: 1px solid var(--border-color);
    text-align: left;
  }

  .report-table-wrapper th {
    background: #f8fafc;
    font-weight: 600;
    color: var(--text-primary);
  }

  .report-table-wrapper tfoot tr {
    background: #f8fafc;
    font-weight: 700;
  }

  .filter-card {
    background: #fff;
    border-radius: var(--border-radius);
    padding: 16px 20px;
    box-shadow: var(--card-shadow);
    margin-bottom: 20px;
    border: 1px solid var(--border-color);
  }

  .report-signatories-container {
    display: flex;
    justify-content: space-between;
    margin-top: 45px;
    padding-top: 20px;
    gap: 40px;
  }

  .signatory-box {
    flex: 1;
    max-width: 320px;
  }

  .signatory-label {
    font-size: 13px;
    color: #4b5563;
    margin-bottom: 40px;
    font-weight: 500;
  }

  .signatory-name {
    font-size: 14px;
    font-weight: 700;
    color: #111;
    text-transform: uppercase;
    border-bottom: 1px solid #111;
    padding-bottom: 4px;
    display: inline-block;
    min-width: 220px;
  }

  .signatory-position {
    font-size: 12px;
    color: #4b5563;
    margin-top: 4px;
  }

  @media print {
    @page {
      size: landscape;
      margin: 12mm 12mm 12mm 12mm;
    }
    body {
      background: #fff !important;
      color: #000 !important;
    }
    .sidebar, .topbar, .page-header, .print-controls, .breadcrumb, .filter-card, .toast-container, .no-print {
      display: none !important;
    }
    .app-layout {
      display: block !important;
    }
    .main-content {
      margin: 0 !important;
      padding: 0 !important;
      width: 100% !important;
      max-width: 100% !important;
    }
    .report-container {
      border: none !important;
      box-shadow: none !important;
      padding: 0 !important;
      margin: 0 !important;
    }
    .report-table-wrapper {
      overflow: visible !important;
      margin-bottom: 20px !important;
    }
    .report-table-wrapper table {
      font-size: 11px !important;
    }
    .report-table-wrapper th,
    .report-table-wrapper td {
      padding: 6px 8px !important;
      border: 1px solid #333 !important;
    }
    .report-table-wrapper th {
      background-color: #eee !important;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }
    .badge {
      border: 1px solid #444 !important;
      background: transparent !important;
      color: #000 !important;
      font-size: 10px !important;
      padding: 1px 4px !important;
    }
    .section-heading {
      color: #000 !important;
      font-size: 13px !important;
      margin: 16px 0 8px 0 !important;
      page-break-after: avoid;
    }
    .official-logo {
      width: 80px !important;
      height: 80px !important;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }
    .official-divider {
      border-top: 2px solid #000 !important;
    }
    .report-section-block {
      page-break-inside: avoid;
    }
    .report-signatories-container {
      display: flex !important;
      justify-content: space-between !important;
      margin-top: 40px !important;
      padding-top: 15px !important;
      page-break-inside: avoid !important;
    }
    .signatory-box {
      flex: 1 !important;
      max-width: 280px !important;
    }
    .signatory-label {
      font-size: 11px !important;
      color: #000 !important;
      margin-bottom: 35px !important;
    }
    .signatory-name {
      font-size: 12px !important;
      font-weight: 700 !important;
      color: #000 !important;
      text-transform: uppercase !important;
      border-bottom: 1px solid #000 !important;
      padding-bottom: 2px !important;
      min-width: 200px !important;
      display: inline-block !important;
    }
    .signatory-position {
      font-size: 11px !important;
      color: #333 !important;
      margin-top: 3px !important;
    }
  }
</style>
<body>
  <div class="app-layout">

    <?php require_once '../../includes/admin-sidebar.php'; ?>

    <?php
    $topbarTitle    = 'Reports';
    $topbarSubtitle = 'Crop insurance summary reports and printable lists';
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
          <h2>Crop Insurance Report</h2>
          <p>Official summary report and printable list records</p>
        </div>
        <div class="page-header-right print-controls" style="display:flex;gap:8px;flex-wrap:wrap">
          <button class="btn btn-outline" onclick="exportReport('policies')">⬇️ Export Applications</button>
          <button class="btn btn-outline" onclick="exportReport('claims')">⬇️ Export Claims</button>
          <button class="btn btn-primary" onclick="window.print()">🖨️ Print Report</button>
        </div>
      </div>

      <!-- Filters Row -->
      <div class="filter-card no-print">
        <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
          <div class="form-group" style="margin-bottom:0;flex:1;min-width:160px">
            <label class="form-label" style="font-size:12px;margin-bottom:4px">Report View</label>
            <select class="form-select" id="filter-view" onchange="toggleReportView()">
              <option value="all">All (Applications & Claims)</option>
              <option value="applications">Applications Only</option>
              <option value="claims">Claims Only</option>
            </select>
          </div>
          <div class="form-group" style="margin-bottom:0;flex:1;min-width:160px">
            <label class="form-label" style="font-size:12px;margin-bottom:4px">Status Filter</label>
            <select class="form-select" id="filter-status" onchange="loadReports()">
              <option value="">All Statuses</option>
              <option value="active">Active / Approved</option>
              <option value="pending">Pending</option>
              <option value="rejected">Rejected</option>
              <option value="cancelled">Cancelled</option>
              <option value="paid">Paid (Claims)</option>
            </select>
          </div>
          <div class="form-group" style="margin-bottom:0;flex:1;min-width:140px">
            <label class="form-label" style="font-size:12px;margin-bottom:4px">Date From</label>
            <input type="date" class="form-control" id="filter-date-from" onchange="loadReports()" />
          </div>
          <div class="form-group" style="margin-bottom:0;flex:1;min-width:140px">
            <label class="form-label" style="font-size:12px;margin-bottom:4px">Date To</label>
            <input type="date" class="form-control" id="filter-date-to" onchange="loadReports()" />
          </div>
          <button class="btn btn-outline" style="height:38px" onclick="resetFilters()">Reset</button>
        </div>
      </div>

      <!-- Signatories Config Card -->
      <div class="filter-card no-print">
        <div style="font-weight:600;font-size:13px;margin-bottom:10px;display:flex;align-items:center;gap:6px">
          <span>✍️ Report Signatories</span>
          <span style="font-size:12px;font-weight:normal;color:var(--text-muted)">(Customize signatories displayed on the report footer and printout)</span>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:12px">
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label" style="font-size:12px;margin-bottom:4px">Created / Prepared By (Name)</label>
            <input type="text" class="form-control" id="input-created-name" placeholder="e.g. Juan Dela Cruz" oninput="updateSignatories()" />
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label" style="font-size:12px;margin-bottom:4px">Prepared By Position</label>
            <input type="text" class="form-control" id="input-created-position" placeholder="e.g. Municipal Agriculturist" oninput="updateSignatories()" />
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label" style="font-size:12px;margin-bottom:4px">Approved By (Name)</label>
            <input type="text" class="form-control" id="input-approved-name" placeholder="e.g. Hon. Vicente G. Pagurayan" oninput="updateSignatories()" />
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label" style="font-size:12px;margin-bottom:4px">Approved By Position</label>
            <input type="text" class="form-control" id="input-approved-position" placeholder="e.g. Municipal Mayor" oninput="updateSignatories()" />
          </div>
        </div>
      </div>

      <!-- Printable Report Document Sheet -->
      <div class="report-container">
        <!-- Official Letterhead Header -->
        <div class="official-report-header">
          <img src="../../img/Agri-Sto-Logo.png" alt="Municipal Agriculture Office Sto. Niño" class="official-logo" />
          <div class="official-text">
            <div class="line">Republic of the Philippines</div>
            <div class="line">Province of Cagayan</div>
            <div class="line">Municipality of Sto. Niño</div>
            <div class="report-title">Crop Insurance Report</div>
          </div>
          <img src="../../img/Municipality-logo.png" alt="Municipality of Sto. Niño" class="official-logo" />
        </div>

        <hr class="official-divider" />

        <div class="report-meta">
          <span><strong>Date Generated:</strong> <span id="gen-date"></span></span>
          <span><strong>Generated By:</strong> <span id="gen-user">Admin</span></span>
        </div>

        <!-- Applications List Block -->
        <div class="report-section-block" id="block-applications">
          <div class="section-heading">
            <span>📋 Applications List</span>
            <span id="app-count" style="font-size:13px;font-weight:normal;color:var(--text-muted)"></span>
          </div>
          <div class="report-table-wrapper">
            <table id="report-app-table">
              <thead>
                <tr>
                  <th style="width:40px">#</th>
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
              <tbody id="app-table-body">
                <tr><td colspan="9" style="text-align:center;padding:16px">Loading applications...</td></tr>
              </tbody>
              <tfoot id="app-table-foot"></tfoot>
            </table>
          </div>
        </div>

        <!-- Claims List Block -->
        <div class="report-section-block" id="block-claims">
          <div class="section-heading">
            <span>📩 Claims List</span>
            <span id="claims-count" style="font-size:13px;font-weight:normal;color:var(--text-muted)"></span>
          </div>
          <div class="report-table-wrapper">
            <table id="report-claim-table">
              <thead>
                <tr>
                  <th style="width:40px">#</th>
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
              <tbody id="claims-table-body">
                <tr><td colspan="9" style="text-align:center;padding:16px">Loading claims...</td></tr>
              </tbody>
              <tfoot id="claims-table-foot"></tfoot>
            </table>
          </div>
        </div>

        <!-- Official Signatories Footer -->
        <div class="report-signatories-container">
          <div class="signatory-box">
            <p class="signatory-label">Prepared / Created by:</p>
            <div class="signatory-name" id="sig-created-name">__________________________</div>
            <div class="signatory-position" id="sig-created-position">Municipal Agriculturist / Staff</div>
          </div>

          <div class="signatory-box">
            <p class="signatory-label">Approved by:</p>
            <div class="signatory-name" id="sig-approved-name">__________________________</div>
            <div class="signatory-position" id="sig-approved-position">Municipal Mayor</div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <?php require_once '../../includes/toast.php'; ?>
  <script>
    initTopbarUser();

    // Set generated date & user
    document.getElementById('gen-date').textContent = new Date().toLocaleString('en-US', {
      year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
    });

    const currentUser = getCurrentUser();
    if (currentUser) {
      document.getElementById('gen-user').textContent = `${currentUser.first_name || ''} ${currentUser.last_name || ''}`.trim() || 'Admin';
    }

    // Signatories initialization and management
    function initSignatories() {
      const saved = JSON.parse(localStorage.getItem('lgu_report_signatories') || '{}');
      const userName = currentUser ? `${currentUser.first_name || ''} ${currentUser.last_name || ''}`.trim() : '';

      const createdName = saved.created_name !== undefined ? saved.created_name : (userName || 'Department Staff');
      const createdPos  = saved.created_position !== undefined ? saved.created_position : 'Municipal Agriculture Office Staff';
      const approvedName = saved.approved_name !== undefined ? saved.approved_name : 'Hon. Vicente G. Pagurayan';
      const approvedPos  = saved.approved_position !== undefined ? saved.approved_position : 'Municipal Mayor';

      document.getElementById('input-created-name').value = createdName;
      document.getElementById('input-created-position').value = createdPos;
      document.getElementById('input-approved-name').value = approvedName;
      document.getElementById('input-approved-position').value = approvedPos;

      renderSignatories(createdName, createdPos, approvedName, approvedPos);
    }

    function updateSignatories() {
      const createdName = document.getElementById('input-created-name').value.trim();
      const createdPos  = document.getElementById('input-created-position').value.trim();
      const approvedName = document.getElementById('input-approved-name').value.trim();
      const approvedPos  = document.getElementById('input-approved-position').value.trim();

      localStorage.setItem('lgu_report_signatories', JSON.stringify({
        created_name: createdName,
        created_position: createdPos,
        approved_name: approvedName,
        approved_position: approvedPos,
      }));

      renderSignatories(createdName, createdPos, approvedName, approvedPos);
    }

    function renderSignatories(cName, cPos, aName, aPos) {
      document.getElementById('sig-created-name').textContent = cName || '__________________________';
      document.getElementById('sig-created-position').textContent = cPos || 'Position / Designation';

      document.getElementById('sig-approved-name').textContent = aName || '__________________________';
      document.getElementById('sig-approved-position').textContent = aPos || 'Position / Designation';
    }

    initSignatories();

    function toggleReportView() {
      const view = document.getElementById('filter-view').value;
      const appBlock = document.getElementById('block-applications');
      const clmBlock = document.getElementById('block-claims');

      if (view === 'applications') {
        appBlock.style.display = 'block';
        clmBlock.style.display = 'none';
      } else if (view === 'claims') {
        appBlock.style.display = 'none';
        clmBlock.style.display = 'block';
      } else {
        appBlock.style.display = 'block';
        clmBlock.style.display = 'block';
      }
    }

    function resetFilters() {
      document.getElementById('filter-view').value = 'all';
      document.getElementById('filter-status').value = '';
      document.getElementById('filter-date-from').value = '';
      document.getElementById('filter-date-to').value = '';
      toggleReportView();
      loadReports();
    }

    async function exportReport(type) {
      showLoading();
      try {
        const token    = getToken();
        const status   = document.getElementById('filter-status').value;
        const dateFrom = document.getElementById('filter-date-from').value;
        const dateTo   = document.getElementById('filter-date-to').value;

        const qParams = new URLSearchParams();
        if (status)   qParams.append('status', status);
        if (dateFrom) qParams.append('date_from', dateFrom);
        if (dateTo)   qParams.append('date_to', dateTo);

        const resp = await fetch(`${API_BASE}/reports/export/${type}?${qParams.toString()}`, {
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
        const status   = document.getElementById('filter-status').value;
        const dateFrom = document.getElementById('filter-date-from').value;
        const dateTo   = document.getElementById('filter-date-to').value;

        const qParams = new URLSearchParams();
        if (status)   qParams.append('status', status);
        if (dateFrom) qParams.append('date_from', dateFrom);
        if (dateTo)   qParams.append('date_to', dateTo);

        const qs = qParams.toString() ? `?${qParams.toString()}` : '';

        // 1. Applications table from /reports/policies
        const polRpt = await api('GET', `/reports/policies${qs}`);
        if (polRpt.success) {
          const records = polRpt.data?.records || [];
          const countEl = document.getElementById('app-count');
          if (countEl) countEl.textContent = records.length + ' record(s)';
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
                    <td>${p.area_hectares ? parseFloat(p.area_hectares).toFixed(2) : '—'}</td>
                    <td>${formatCurrency(p.coverage_amount)}</td>
                    <td>${getStatusBadge(p.status)}</td>
                    <td>${formatDate(p.created_at)}</td>
                  </tr>`).join('')
              : '<tr><td colspan="9" style="text-align:center;color:var(--text-muted);padding:16px">No applications found.</td></tr>';
          }
          const foot = document.getElementById('app-table-foot');
          if (foot) {
            if (records.length) {
              const totalArea     = records.reduce((s, p) => s + parseFloat(p.area_hectares || 0), 0);
              const totalCoverage = records.reduce((s, p) => s + parseFloat(p.coverage_amount || 0), 0);
              foot.innerHTML = `<tr>
                <td colspan="5" style="text-align:right;font-weight:700">Totals:</td>
                <td style="font-weight:700">${totalArea.toFixed(2)} ha</td>
                <td style="font-weight:700">${formatCurrency(totalCoverage)}</td>
                <td colspan="2"></td>
              </tr>`;
            } else {
              foot.innerHTML = '';
            }
          }
        }

        // 2. Claims table from /reports/claims
        const clmRpt = await api('GET', `/reports/claims${qs}`);
        if (clmRpt.success) {
          const records = clmRpt.data?.records || [];
          const claimsCount = document.getElementById('claims-count');
          if (claimsCount) claimsCount.textContent = records.length + ' record(s)';

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
              : '<tr><td colspan="9" style="text-align:center;color:var(--text-muted);padding:16px">No claims found.</td></tr>';
          }

          const claimsFoot = document.getElementById('claims-table-foot');
          if (claimsFoot) {
            if (records.length) {
              const totalIndemnity = records
                .filter(c => ['approved', 'paid'].includes(c.status))
                .reduce((s, c) => s + parseFloat(c.approved_amount || 0), 0);
              claimsFoot.innerHTML = `<tr>
                <td colspan="7" style="text-align:right;font-weight:700">Total Approved Indemnity:</td>
                <td style="font-weight:700">${formatCurrency(totalIndemnity)}</td>
                <td></td>
              </tr>`;
            } else {
              claimsFoot.innerHTML = '';
            }
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
