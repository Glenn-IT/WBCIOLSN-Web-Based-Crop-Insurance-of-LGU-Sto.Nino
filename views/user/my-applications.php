<?php require_once '../../components/under-construction.php'; ?>
<?php
$pageTitle   = 'My Applications — Crop Insurance';
$basePath    = '../../';
$currentPage = 'my-applications';
$guardRole   = 'user';
require_once '../../includes/auth-guard.php';
require_once '../../includes/head.php';
?>
<body>
  <div class="app-layout">

    <?php require_once '../../includes/user-sidebar.php'; ?>

    <?php
    $topbarTitle    = 'My Applications';
    $topbarSubtitle = 'View and manage your applications';
    $isAdmin        = false;
    require_once '../../includes/topbar.php';
    ?>

    <main class="main-content">
      <div class="page-header">
        <div class="page-header-left">
          <div class="breadcrumb">
            <span>Home</span><span class="sep">›</span>
            <span class="current">My Applications</span>
          </div>
          <h2>My Applications</h2>
          <p>All your submitted crop insurance applications</p>
        </div>
        <button class="btn btn-primary" onclick="navigateTo('new-application.php')">
          📝 New Application
        </button>
      </div>

      <!-- Stats Row -->
      <div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px">
        <div class="stat-card" style="--stat-color:var(--primary)">
          <div class="stat-icon" style="background:#e8f5e9;color:var(--primary)">📋</div>
          <div class="stat-info"><h3 id="total-apps">0</h3><p>Total Applications</p></div>
        </div>
        <div class="stat-card" style="--stat-color:var(--success)">
          <div class="stat-icon" style="background:#d4edda;color:var(--success)">✅</div>
          <div class="stat-info"><h3 id="approved-apps">0</h3><p>Approved</p></div>
        </div>
        <div class="stat-card" style="--stat-color:var(--warning)">
          <div class="stat-icon" style="background:#fff3cd;color:#856404">⏳</div>
          <div class="stat-info"><h3 id="pending-apps">0</h3><p>Pending</p></div>
        </div>
        <div class="stat-card" style="--stat-color:var(--danger,#dc3545)">
          <div class="stat-icon" style="background:#fce4ec;color:#c62828">❌</div>
          <div class="stat-info"><h3 id="cancelled-apps">0</h3><p>Cancelled</p></div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h5>📁 Application Records</h5>
          <div class="search-filter-bar" style="flex:1;margin-left:20px">
            <div class="search-input-wrapper">
              <span class="search-icon">🔍</span>
              <input type="text" class="form-control" id="search-input"
                placeholder="Search by ID or location..." oninput="filterApps()" />
            </div>
            <select class="form-select" id="status-filter" onchange="filterApps()" style="width:160px">
              <option value="">All Status</option>
              <option value="pending">Pending</option>
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
                <th>Application ID</th>
                <th>Type</th>
                <th>Farm Name</th>
                <th>Area (ha)</th>
                <th>Coverage</th>
                <th>Date Filed</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="apps-table-body"></tbody>
          </table>
        </div>
        <div id="empty-state-apps" class="empty-state hidden">
          <div class="empty-icon">📋</div>
          <h4>No Applications Found</h4>
          <p>You haven't submitted any applications yet.</p>
          <button class="btn btn-primary" style="margin-top:12px"
            onclick="navigateTo('new-application.php')">📝 Apply Now</button>
        </div>
        <div id="pagination-bar" style="display:flex;justify-content:flex-end;gap:8px;padding:12px 16px"></div>
      </div>
    </main>
  </div>

  <!-- View Detail Modal -->
  <div class="modal-overlay" id="view-modal">
    <div class="modal modal-lg">
      <div class="modal-header">
        <h4>📋 Application Details</h4>
        <button class="modal-close" onclick="closeModal('view-modal')">×</button>
      </div>
      <div class="modal-body" id="view-modal-body"></div>
      <div class="modal-footer">
        <button class="btn btn-ghost" onclick="closeModal('view-modal')">Close</button>
        <button class="btn btn-primary" id="claim-from-modal-btn"
          onclick="navigateTo('file-claim.php')">📩 File Claim</button>
      </div>
    </div>
  </div>

  <!-- Edit Modal -->
  <div class="modal-overlay" id="edit-modal" style="align-items:flex-start;padding:20px;overflow-y:auto">
    <div class="modal" style="max-width:780px;width:100%;margin:auto">
      <div class="modal-header">
        <h4>✏️ Edit Application</h4>
        <button class="modal-close" onclick="closeModal('edit-modal')">×</button>
      </div>
      <div class="modal-body" style="padding:24px">
        <input type="hidden" id="edit-app-id" />
        <input type="hidden" id="edit-farm-id" />

        <!-- Edit Stepper -->
        <div class="stepper" id="edit-stepper" style="margin-bottom:24px">
          <div class="step active" id="edit-step-1">
            <div class="step-circle">1</div><div class="step-label">Basic Info</div>
          </div>
          <div class="step" id="edit-step-2">
            <div class="step-circle">2</div><div class="step-label">Farm Info</div>
          </div>
          <div class="step" id="edit-step-3">
            <div class="step-circle">3</div><div class="step-label">Damage Report</div>
          </div>
          <div class="step" id="edit-step-4">
            <div class="step-circle">4</div><div class="step-label">Coverage</div>
          </div>
        </div>

        <!-- Edit Step 1 -->
        <div id="edit-content-1">
          <div class="card" style="box-shadow:none;border:1px solid var(--border-color)">
            <div class="card-header">
              <h5>📋 Step 1: Basic Information</h5>
              <span class="badge badge-primary">Step 1 of 4</span>
            </div>
            <div class="card-body">
              <div class="form-section-title">👤 Applicant Details</div>
              <div class="form-row form-row-2">
                <div class="form-group">
                  <label class="form-label">Application Type *</label>
                  <select id="edit-appType" class="form-select">
                    <option value="">Select type</option>
                    <option value="New">New</option>
                    <option value="Renewal">Renewal</option>
                  </select>
                </div>
                <div class="form-group">
                  <label class="form-label">Date of Application *</label>
                  <input type="date" id="edit-dateApp" class="form-control" />
                </div>
              </div>
              <div class="form-row form-row-2">
                <div class="form-group">
                  <label class="form-label">Farmer Category *</label>
                  <select id="edit-farmerCategory" class="form-select">
                    <option value="">Select category</option>
                    <option>Small Farmer</option>
                    <option>Commercial Farmer</option>
                    <option>Agrarian Reform Beneficiary</option>
                    <option>Fisherfolk</option>
                  </select>
                </div>
                <div class="form-group">
                  <label class="form-label">Crop Type *</label>
                  <select id="edit-cropType" class="form-select">
                    <option value="">Select crop type</option>
                    <option value="1">Rice</option>
                    <option value="2">Corn</option>
                    <option value="3">Sugarcane</option>
                    <option value="4">Banana</option>
                    <option value="5">Coconut</option>
                    <option value="6">Vegetables</option>
                    <option value="7">Cassava</option>
                    <option value="8">Coffee</option>
                    <option value="9">Mango</option>
                    <option value="10">Tobacco</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Full Name of Applicant *</label>
                <input type="text" id="edit-farmerName" class="form-control" placeholder="First Middle Last" />
              </div>
            </div>
          </div>
          <div style="display:flex;justify-content:flex-end;margin-top:14px">
            <button class="btn btn-primary btn-lg" onclick="editNextStep()">Next: Farm Information →</button>
          </div>
        </div>

        <!-- Edit Step 2 -->
        <div id="edit-content-2" style="display:none">
          <div class="card" style="box-shadow:none;border:1px solid var(--border-color)">
            <div class="card-header">
              <h5>🌾 Step 2: Farm Information</h5>
              <span class="badge badge-primary">Step 2 of 4</span>
            </div>
            <div class="card-body">
              <div class="form-section-title">📍 Location &amp; Area</div>
              <div class="form-group">
                <label class="form-label">Farm Location *</label>
                <input type="text" id="edit-location" class="form-control"
                  placeholder="Barangay, Municipality, Province" />
              </div>
              <div class="form-row form-row-3">
                <div class="form-group">
                  <label class="form-label">Total Area (hectares) *</label>
                  <input type="number" id="edit-area" class="form-control" placeholder="e.g. 2.5" step="0.1" />
                </div>
                <div class="form-group">
                  <label class="form-label">Land Category *</label>
                  <select id="edit-landCategory" class="form-select">
                    <option value="">Select</option>
                    <option>Irrigated</option>
                    <option>Rain-fed</option>
                    <option>Upland</option>
                  </select>
                </div>
                <div class="form-group">
                  <label class="form-label">Tenurial Status *</label>
                  <select id="edit-tenurial" class="form-select">
                    <option value="">Select</option>
                    <option>Owner</option>
                    <option>Lessee</option>
                    <option>Tenant</option>
                    <option>Caretaker</option>
                  </select>
                </div>
              </div>
              <div class="form-row form-row-3">
                <div class="form-group">
                  <label class="form-label">Planting Date *</label>
                  <input type="date" id="edit-plantingDate" class="form-control" />
                </div>
                <div class="form-group">
                  <label class="form-label">Expected Harvest Date *</label>
                  <input type="date" id="edit-harvestDate" class="form-control" />
                </div>
                <div class="form-group">
                  <label class="form-label">Planting Method *</label>
                  <select id="edit-plantingMethod" class="form-select">
                    <option value="">Select</option>
                    <option>Direct Seeding</option>
                    <option>Transplanting</option>
                    <option>Broadcast</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div style="display:flex;justify-content:space-between;margin-top:14px">
            <button class="btn btn-ghost btn-lg" onclick="editPrevStep()">← Back</button>
            <button class="btn btn-primary btn-lg" onclick="editNextStep()">Next: Damage Report →</button>
          </div>
        </div>

        <!-- Edit Step 3 -->
        <div id="edit-content-3" style="display:none">
          <div class="card" style="box-shadow:none;border:1px solid var(--border-color)">
            <div class="card-header">
              <h5>⚠️ Step 3: Damage Report</h5>
              <span class="badge badge-primary">Step 3 of 4</span>
            </div>
            <div class="card-body">
              <div class="form-section-title">🌪️ Damage Details</div>
              <div class="form-row form-row-2">
                <div class="form-group">
                  <label class="form-label">Date of Loss *</label>
                  <input type="date" id="edit-dateOfLoss" class="form-control" />
                </div>
                <div class="form-group">
                  <label class="form-label">Cause of Damage *</label>
                  <select id="edit-cause" class="form-select">
                    <option value="">Select cause</option>
                    <option>Flood</option>
                    <option>Drought</option>
                    <option>Typhoon</option>
                    <option>Pest/Disease</option>
                    <option>Fire</option>
                    <option>Others</option>
                  </select>
                </div>
              </div>
              <div class="form-row form-row-2">
                <div class="form-group">
                  <label class="form-label">Percentage of Damage (%) *</label>
                  <input type="number" id="edit-percent" class="form-control"
                    placeholder="e.g. 75" min="1" max="100" />
                </div>
                <div class="form-group">
                  <label class="form-label">Financial Damage (₱) *</label>
                  <input type="number" id="edit-financial" class="form-control" placeholder="e.g. 45000" />
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Description of Damage</label>
                <textarea id="edit-damageDesc" class="form-control"
                  placeholder="Describe the extent of damage in detail..."></textarea>
              </div>
            </div>
          </div>
          <div style="display:flex;justify-content:space-between;margin-top:14px">
            <button class="btn btn-ghost btn-lg" onclick="editPrevStep()">← Back</button>
            <button class="btn btn-primary btn-lg" onclick="editNextStep()">Next: Coverage →</button>
          </div>
        </div>

        <!-- Edit Step 4 -->
        <div id="edit-content-4" style="display:none">
          <div class="card" style="box-shadow:none;border:1px solid var(--border-color)">
            <div class="card-header">
              <h5>🔒 Step 4: Coverage</h5>
              <span class="badge badge-primary">Step 4 of 4</span>
            </div>
            <div class="card-body">
              <div class="form-section-title">💰 Coverage Details</div>
              <div class="form-row form-row-2">
                <div class="form-group">
                  <label class="form-label">Desired Coverage Amount (₱) *</label>
                  <input type="number" id="edit-coverage" class="form-control" placeholder="e.g. 50000" />
                </div>
                <div class="form-group">
                  <label class="form-label">Coverage Period</label>
                  <select id="edit-coveragePeriod" class="form-select">
                    <option>1 Cropping Season</option>
                    <option>1 Year</option>
                    <option>2 Years</option>
                  </select>
                </div>
              </div>
              <div class="form-section-title" style="margin-top:20px">☑️ Confirmation</div>
              <div style="background:#f8fafc;border-radius:10px;padding:16px;border:1px solid var(--border-color);
                font-size:13px;color:var(--text-secondary);line-height:1.7;margin-bottom:14px">
                I confirm that the updated information provided is accurate and I understand that changes
                are subject to re-review by the LGU.
              </div>
              <div class="form-group">
                <label class="form-check">
                  <input type="checkbox" id="edit-consent-check" />
                  <span style="font-size:13.5px">I confirm all updated information is accurate and truthful.</span>
                </label>
              </div>
            </div>
          </div>
          <div style="display:flex;justify-content:space-between;margin-top:14px">
            <button class="btn btn-ghost btn-lg" onclick="editPrevStep()">← Back</button>
            <button class="btn btn-primary btn-lg" id="edit-save-btn" onclick="saveEdit()">
              💾 Save Changes
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Cancel / Delete Confirmation Modal -->
  <div class="modal-overlay" id="confirm-action-modal" style="z-index:2000">
    <div class="modal" style="max-width:420px;width:100%">
      <div class="modal-header" style="background:#fff5f5;border-bottom:1px solid #fce4ec;padding:18px 20px">
        <h4 style="color:#c62828;margin:0;font-size:16px">
          <span id="confirm-modal-icon">⚠️</span>
          <span id="confirm-modal-title" style="margin-left:6px">Confirm Action</span>
        </h4>
        <button class="modal-close" onclick="closeConfirmModal()" style="color:#c62828">×</button>
      </div>
      <div class="modal-body" style="padding:24px 20px">
        <p id="confirm-modal-message" style="margin:0 0 6px;font-size:14.5px;line-height:1.6;color:var(--text-primary)"></p>
        <p style="margin:0;font-size:12.5px;color:var(--text-muted)">This action cannot be undone.</p>
      </div>
      <div class="modal-footer" style="gap:10px">
        <button class="btn btn-ghost" onclick="closeConfirmModal()">Go Back</button>
        <button class="btn" id="confirm-modal-btn"
          style="background:#c62828;color:#fff;border:none;padding:8px 20px;border-radius:8px;font-size:14px;cursor:pointer"
          onclick="executeConfirmAction()">Confirm</button>
      </div>
    </div>
  </div>

  <?php require_once '../../includes/toast.php'; ?>
  <script>
    initTopbarUser();

    let allApps       = [];
    let currentEditId = null;

    async function loadApplications() {
      try {
        const res = await api('GET', '/policies');
        if (!res.success) { showToast('Error', 'Failed to load applications.', 'error'); return; }
        allApps = res.data || [];
        updateStats(allApps);
        renderTable(allApps);
      } catch (err) {
        console.error(err);
        showToast('Error', 'Error loading applications.', 'error');
      }
    }

    function updateStats(apps) {
      document.getElementById('total-apps').textContent     = apps.length;
      document.getElementById('approved-apps').textContent  = apps.filter(a => a.status === 'active').length;
      document.getElementById('pending-apps').textContent   = apps.filter(a => a.status === 'pending').length;
      document.getElementById('cancelled-apps').textContent = apps.filter(a => a.status === 'cancelled').length;
    }

    function renderTable(apps) {
      const tbody = document.getElementById('apps-table-body');
      const empty = document.getElementById('empty-state-apps');
      if (!tbody) return;

      if (apps.length === 0) {
        tbody.innerHTML = '';
        if (empty) empty.classList.remove('hidden');
        return;
      }
      if (empty) empty.classList.add('hidden');

      tbody.innerHTML = apps.map(p => {
        const isPending     = p.status === 'pending';
        const isCancellable = ['pending', 'active'].includes(p.status);
        const safeNum       = (p.policy_number || 'APP-' + p.id).replace(/'/g, "\\'");

        return `
          <tr>
            <td><strong>${p.policy_number || 'APP-' + p.id}</strong></td>
            <td>${p.plan_name || '—'}</td>
            <td>${p.farm_name || '—'}</td>
            <td>${p.area_hectares ? p.area_hectares + ' ha' : '—'}</td>
            <td>${formatCurrency(p.coverage_amount)}</td>
            <td>${formatDate(p.created_at)}</td>
            <td>${getStatusBadge(p.status)}</td>
            <td>
              <div style="display:flex;gap:6px;flex-wrap:wrap">
                <button class="btn btn-sm"
                  style="background:#e3f2fd;color:#1565c0;border:none;border-radius:6px;padding:4px 10px;cursor:pointer"
                  onclick="viewApp(${p.id})">👁 View</button>
                ${isPending ? `<button class="btn btn-sm"
                  style="background:#fff3e0;color:#e65100;border:none;border-radius:6px;padding:4px 10px;cursor:pointer"
                  onclick="openEdit(${p.id})">✏️ Edit</button>` : ''}
                ${isCancellable ? `<button class="btn btn-sm"
                  style="background:#fce4ec;color:#c62828;border:none;border-radius:6px;padding:4px 10px;cursor:pointer"
                  onclick="confirmCancel(${p.id},'${safeNum}')">❌ Cancel</button>` : ''}
                ${!isCancellable ? `<button class="btn btn-sm"
                  style="background:#ffebee;color:#b71c1c;border:none;border-radius:6px;padding:4px 10px;cursor:pointer"
                  onclick="confirmDelete(${p.id},'${safeNum}')">🗑 Delete</button>` : ''}
              </div>
            </td>
          </tr>`;
      }).join('');
    }

    function filterApps() {
      const q  = (document.getElementById('search-input').value  || '').toLowerCase();
      const st = (document.getElementById('status-filter').value || '').toLowerCase();
      const filtered = allApps.filter(p => {
        const matchQ  = !q  || (p.policy_number || '').toLowerCase().includes(q)
                             || (p.farm_name    || '').toLowerCase().includes(q)
                             || (p.plan_name    || '').toLowerCase().includes(q);
        const matchSt = !st || (p.status || '').toLowerCase() === st;
        return matchQ && matchSt;
      });
      renderTable(filtered);
    }

    function sectionTitle(icon, label) {
      return `<div style="grid-column:1/-1;margin-top:8px;padding-bottom:4px;
        border-bottom:1px solid var(--border-color);font-size:12px;font-weight:700;
        color:var(--text-muted);letter-spacing:.5px;text-transform:uppercase">
        ${icon} ${label}</div>`;
    }
    function detailCell(label, value) {
      return `<div><span style="font-size:11px;color:var(--text-muted);display:block">${label}</span>
        <strong style="font-size:13.5px">${value || '—'}</strong></div>`;
    }

    async function viewApp(id) {
      try {
        const res = await api('GET', `/policies/${id}`);
        if (!res.success) { showToast('Error', 'Failed to load details.', 'error'); return; }
        const p = res.data;

        const geoText = (p.latitude && p.longitude)
          ? `<a href="https://www.openstreetmap.org/?mlat=${p.latitude}&mlon=${p.longitude}&zoom=15"
               target="_blank" style="color:var(--primary);font-size:13px">
               📍 ${parseFloat(p.latitude).toFixed(5)}, ${parseFloat(p.longitude).toFixed(5)} (view map)
             </a>`
          : '—';

        document.getElementById('view-modal-body').innerHTML = `
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px 20px">

            ${sectionTitle('📋', 'Application')}
            ${detailCell('Policy Number', p.policy_number || 'APP-' + p.id)}
            ${detailCell('Status', getStatusBadge(p.status))}
            ${detailCell('Application Type', p.application_type || '—')}
            ${detailCell('Date Filed', formatDate(p.created_at))}

            ${sectionTitle('🌾', 'Farm Information')}
            ${detailCell('Farm Name', p.farm_name)}
            ${detailCell('Farm Location', p.farm_location)}
            ${detailCell('Total Area', p.area_hectares ? p.area_hectares + ' ha' : '—')}
            ${detailCell('Crop Type', p.crop_type)}
            ${detailCell('Land Category', p.land_category)}
            ${detailCell('Tenurial Status', p.tenurial_status)}
            ${detailCell('Planting Method', p.planting_method)}
            ${detailCell('Planting Date', formatDate(p.planting_date))}
            ${detailCell('Expected Harvest', formatDate(p.harvest_date))}
            <div style="grid-column:1/-1">${detailCell('Geo Tag', geoText)}</div>

            ${sectionTitle('⚠️', 'Damage Report')}
            ${detailCell('Date of Loss', formatDate(p.date_of_loss))}
            ${detailCell('Cause of Damage', p.cause_of_damage)}
            ${detailCell('Percentage of Damage', p.percent_damage ? p.percent_damage + '%' : '—')}
            ${detailCell('Financial Damage', p.financial_damage ? formatCurrency(p.financial_damage) : '—')}
            ${p.damage_description
              ? `<div style="grid-column:1/-1">
                   <span style="font-size:11px;color:var(--text-muted);display:block">Description</span>
                   <span style="font-size:13px">${p.damage_description}</span>
                 </div>`
              : ''}

            ${sectionTitle('💰', 'Coverage')}
            ${detailCell('Plan', p.plan_name)}
            ${detailCell('Coverage Type', (p.coverage_type || '').replace(/_/g,' '))}
            ${detailCell('Coverage Amount', formatCurrency(p.coverage_amount))}
            ${detailCell('Total Premium', formatCurrency(p.total_premium))}
            ${detailCell('Start Date', formatDate(p.start_date))}
            ${detailCell('End Date', formatDate(p.end_date))}
            ${p.agent_name  ? detailCell('Assigned Agent', p.agent_name) : ''}
            ${p.approved_at ? detailCell('Approved At', formatDate(p.approved_at)) : ''}
          </div>
          ${p.remarks
            ? `<div style="margin-top:14px;padding:10px 14px;background:#fff3cd;
                border:1px solid #ffc107;border-radius:8px;font-size:13px">
                <strong>📝 Remarks:</strong> ${p.remarks}</div>`
            : ''}`;

        const claimBtn = document.getElementById('claim-from-modal-btn');
        if (claimBtn) {
          claimBtn.style.display = p.status === 'active' ? '' : 'none';
          claimBtn.onclick = () => navigateTo('file-claim.php?policy_id=' + p.id);
        }
        openModal('view-modal');
      } catch (err) {
        console.error(err);
        showToast('Error', 'Error loading application details.', 'error');
      }
    }

    let editCurrentStep   = 1;
    const EDIT_TOTAL_STEPS = 4;

    function showEditStep(step) {
      for (let i = 1; i <= EDIT_TOTAL_STEPS; i++) {
        const c = document.getElementById('edit-content-' + i);
        const s = document.getElementById('edit-step-'   + i);
        if (c) c.style.display = i === step ? 'block' : 'none';
        if (s) {
          s.classList.toggle('active',    i === step);
          s.classList.toggle('completed', i < step);
        }
      }
      editCurrentStep = step;
    }

    function editNextStep() {
      if (editCurrentStep === 1) {
        if (!document.getElementById('edit-appType').value)
          { showToast('Validation', 'Please select Application Type.', 'warning'); return; }
        if (!document.getElementById('edit-farmerName').value.trim())
          { showToast('Validation', 'Please enter the Farmer Full Name.', 'warning'); return; }
        if (!document.getElementById('edit-cropType').value)
          { showToast('Validation', 'Please select a Crop Type.', 'warning'); return; }
      }
      if (editCurrentStep === 2) {
        if (!document.getElementById('edit-location').value.trim())
          { showToast('Validation', 'Please enter Farm Location.', 'warning'); return; }
        if (!parseFloat(document.getElementById('edit-area').value))
          { showToast('Validation', 'Please enter a valid Total Area.', 'warning'); return; }
      }
      if (editCurrentStep === 3) {
        if (!document.getElementById('edit-cause').value)
          { showToast('Validation', 'Please select Cause of Damage.', 'warning'); return; }
      }
      if (editCurrentStep < EDIT_TOTAL_STEPS) showEditStep(editCurrentStep + 1);
    }

    function editPrevStep() { if (editCurrentStep > 1) showEditStep(editCurrentStep - 1); }

    async function openEdit(id) {
      try {
        const polRes = await api('GET', `/policies/${id}`);
        if (!polRes.success) { showToast('Error', 'Failed to load application.', 'error'); return; }
        const p = polRes.data;

        if (p.status !== 'pending') { showToast('Notice', 'Only pending applications can be edited.', 'warning'); return; }

        currentEditId = id;
        document.getElementById('edit-app-id').value  = id;
        document.getElementById('edit-farm-id').value = p.farm_id || '';

        let farm = {};
        if (p.farm_id) {
          const farmRes = await api('GET', `/farms/${p.farm_id}`);
          if (farmRes.success && farmRes.data) farm = farmRes.data;
        }

        document.getElementById('edit-farmerName').value    = ((p.first_name || '') + ' ' + (p.last_name || '')).trim();
        document.getElementById('edit-cropType').value      = farm.crop_type_id || '';
        document.getElementById('edit-dateApp').value       = (p.start_date || '').substring(0, 10);
        document.getElementById('edit-appType').value       = farm.application_type || '';
        document.getElementById('edit-farmerCategory').value= farm.farmer_category || '';

        document.getElementById('edit-location').value      = farm.location || p.location || '';
        document.getElementById('edit-area').value          = farm.area_hectares || p.area_hectares || '';
        document.getElementById('edit-landCategory').value  = farm.soil_type || '';
        document.getElementById('edit-tenurial').value      = farm.tenurial_status || '';
        document.getElementById('edit-plantingDate').value  = farm.planting_date
          ? farm.planting_date.substring(0, 10) : (p.start_date || '').substring(0, 10);
        document.getElementById('edit-harvestDate').value   = farm.harvest_date
          ? farm.harvest_date.substring(0, 10) : (p.end_date || '').substring(0, 10);
        document.getElementById('edit-plantingMethod').value= farm.planting_method || '';

        document.getElementById('edit-cause').value         = p.cause_of_damage || '';
        document.getElementById('edit-percent').value       = p.percent_damage || '';
        document.getElementById('edit-financial').value     = p.financial_damage || '';
        document.getElementById('edit-damageDesc').value    = p.damage_description || '';
        document.getElementById('edit-dateOfLoss').value    = p.date_of_loss ? p.date_of_loss.substring(0, 10) : '';

        document.getElementById('edit-coverage').value      = p.coverage_amount || '';
        document.getElementById('edit-consent-check').checked = false;

        showEditStep(1);
        openModal('edit-modal');
      } catch (err) {
        console.error(err);
        showToast('Error', 'Error loading application.', 'error');
      }
    }

    async function saveEdit() {
      if (!document.getElementById('edit-consent-check').checked) {
        showToast('Notice', 'Please confirm the accuracy of your information.', 'warning'); return;
      }
      const farmId        = document.getElementById('edit-farm-id').value;
      const farmerName    = document.getElementById('edit-farmerName').value.trim();
      const location      = document.getElementById('edit-location').value.trim();
      const area          = parseFloat(document.getElementById('edit-area').value) || 0;
      const cropTypeId    = parseInt(document.getElementById('edit-cropType').value) || 0;

      if (!location)   { showToast('Validation', 'Farm location is required.', 'warning'); showEditStep(2); return; }
      if (!area)       { showToast('Validation', 'Total area is required.', 'warning');    showEditStep(2); return; }
      if (!cropTypeId) { showToast('Validation', 'Please select a Crop Type.', 'warning'); showEditStep(1); return; }
      if (!farmerName) { showToast('Validation', 'Farmer name is required.', 'warning');   showEditStep(1); return; }

      const farmPayload = {
        farm_name:        farmerName + ' Farm',
        application_type: document.getElementById('edit-appType').value,
        farmer_category:  document.getElementById('edit-farmerCategory').value,
        location, area_hectares: area, crop_type_id: cropTypeId,
        soil_type:        document.getElementById('edit-landCategory').value,
        tenurial_status:  document.getElementById('edit-tenurial').value,
        planting_method:  document.getElementById('edit-plantingMethod').value,
        planting_date:    document.getElementById('edit-plantingDate').value,
        harvest_date:     document.getElementById('edit-harvestDate').value,
      };

      const btn = document.getElementById('edit-save-btn');
      if (btn) { btn.disabled = true; btn.textContent = '⏳ Saving...'; }

      try {
        if (farmId) {
          const farmRes = await api('PUT', `/farms/${farmId}`, farmPayload);
          if (!farmRes.success) {
            showToast('Error', farmRes.message || 'Failed to update farm details.', 'error'); return;
          }
        }

        const policyRes = await api('PUT', `/policies/${currentEditId}`, {
          cause_of_damage:    document.getElementById('edit-cause').value,
          percent_damage:     parseFloat(document.getElementById('edit-percent').value)   || null,
          financial_damage:   parseFloat(document.getElementById('edit-financial').value) || null,
          damage_description: document.getElementById('edit-damageDesc').value,
          date_of_loss:       document.getElementById('edit-dateOfLoss').value,
          coverage_amount:    parseFloat(document.getElementById('edit-coverage').value)  || null,
        });
        if (!policyRes.success) {
          showToast('Error', policyRes.message || 'Failed to update damage details.', 'error'); return;
        }

        showToast('Success', 'Application updated successfully.', 'success');
        closeModal('edit-modal');
        loadApplications();
      } catch (err) {
        console.error(err);
        showToast('Error', 'Error saving changes.', 'error');
      } finally {
        if (btn) { btn.disabled = false; btn.textContent = '💾 Save Changes'; }
      }
    }

    let pendingConfirmAction = null;

    function openConfirmModal(icon, title, message, btnLabel, btnColor, action) {
      document.getElementById('confirm-modal-icon').textContent    = icon;
      document.getElementById('confirm-modal-title').textContent   = title;
      document.getElementById('confirm-modal-message').textContent = message;
      const btn = document.getElementById('confirm-modal-btn');
      btn.textContent          = btnLabel;
      btn.style.background     = btnColor;
      pendingConfirmAction     = action;
      openModal('confirm-action-modal');
    }

    function closeConfirmModal() {
      pendingConfirmAction = null;
      closeModal('confirm-action-modal');
    }

    function executeConfirmAction() {
      closeModal('confirm-action-modal');
      if (typeof pendingConfirmAction === 'function') pendingConfirmAction();
      pendingConfirmAction = null;
    }

    function confirmCancel(id, policyNumber) {
      openConfirmModal(
        '❌', 'Cancel Application',
        `Are you sure you want to cancel application ${policyNumber}?`,
        'Yes, Cancel It', '#c62828',
        () => cancelApp(id)
      );
    }

    async function cancelApp(id) {
      try {
        const res = await api('PUT', `/policies/${id}/cancel`, { remarks: 'Cancelled by user.' });
        if (!res.success) { showToast('Error', res.message || 'Failed to cancel application.', 'error'); return; }
        showToast('Cancelled', 'Application cancelled successfully.', 'success');
        loadApplications();
      } catch (err) {
        console.error(err);
        showToast('Error', 'Error cancelling application.', 'error');
      }
    }

    function confirmDelete(id, policyNumber) {
      openConfirmModal(
        '🗑️', 'Remove Application',
        `Remove application ${policyNumber} from your list? It will be hidden from your view.`,
        'Yes, Remove', '#6c757d',
        () => {
          allApps = allApps.filter(p => p.id !== id);
          updateStats(allApps);
          filterApps();
          showToast('Removed', 'Application removed from list.', 'success');
        }
      );
    }

    loadApplications();
  </script>
</body>
</html>
