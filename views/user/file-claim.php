<?php require_once '../../components/under-construction.php'; ?>
<?php
$pageTitle   = 'File a Claim — Crop Insurance';
$basePath    = '../../';
$currentPage = 'file-claim';
$guardRole   = 'user';
require_once '../../includes/auth-guard.php';
require_once '../../includes/head.php';
?>
<body>
  <div class="app-layout">

    <?php require_once '../../includes/user-sidebar.php'; ?>

    <?php
    $topbarTitle    = 'File a Claim';
    $topbarSubtitle = 'Submit a new insurance claim for your damaged crops';
    $isAdmin        = false;
    require_once '../../includes/topbar.php';
    ?>

    <main class="main-content">
      <div class="page-header">
        <div class="page-header-left">
          <div class="breadcrumb">
            <span>Home</span><span class="sep">›</span>
            <span class="current">File a Claim</span>
          </div>
          <h2>Insurance Claim Form</h2>
          <p>Submit a claim for your approved applications</p>
        </div>
      </div>

      <div class="grid-2" style="align-items:start">
        <!-- Claim Form -->
        <div class="card">
          <div class="card-header"><h5>📩 Claim Details</h5></div>
          <div class="card-body">
            <div class="form-section-title">📋 Application Reference</div>
            <div class="form-group">
              <label class="form-label">Select Application *</label>
              <select id="claim-app-id" class="form-select" onchange="populateFromApp()">
                <option value="">Select application ID</option>
              </select>
            </div>
            <div id="app-info-box"
              style="display:none;background:#e8f5e9;border-radius:8px;padding:14px;
                margin-bottom:16px;border:1px solid #a5d6a7;font-size:13px">
              <div id="app-info-content"></div>
            </div>

            <div class="form-section-title">⚠️ Damage Information</div>
            <div class="form-row form-row-2">
              <div class="form-group">
                <label class="form-label">Date of Claim Submitted *</label>
                <input type="date" id="claim-date" class="form-control" />
              </div>
              <div class="form-group">
                <label class="form-label">Date of Loss *</label>
                <input type="date" id="claim-loss-date" class="form-control" />
              </div>
            </div>
            <div class="form-row form-row-2">
              <div class="form-group">
                <label class="form-label">Cause of Damage *</label>
                <select id="claim-cause" class="form-select">
                  <option value="">Select cause</option>
                  <option>Flood</option>
                  <option>Drought</option>
                  <option>Typhoon</option>
                  <option>Pest/Disease</option>
                  <option>Fire</option>
                  <option>Others</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Damage Percentage (%) *</label>
                <input type="number" id="claim-percent" class="form-control"
                  placeholder="e.g. 75" min="1" max="100" />
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Description of Damage *</label>
              <textarea id="claim-description" class="form-control" rows="4"
                placeholder="Describe in detail the extent and nature of the damage..."></textarea>
            </div>

            <div class="form-section-title">📸 Photo Evidence</div>
            <div class="file-upload-area" id="photo-drop">
              <input type="file" id="claim-photo" accept="image/*" onchange="previewClaimPhoto(this)" />
              <div class="file-upload-icon">📸</div>
              <p class="file-upload-text"><span>Click to upload</span> damage photos</p>
              <p class="file-upload-text">PNG, JPG, JPEG up to 10MB</p>
            </div>
            <div id="claim-photo-preview" style="display:none;margin-top:10px">
              <img id="claim-preview-img"
                style="max-width:100%;max-height:150px;border-radius:8px;border:1px solid var(--border-color)" />
              <p style="font-size:12px;color:var(--success);margin-top:6px">✅ Photo uploaded successfully</p>
            </div>

            <div style="display:flex;gap:10px;margin-top:20px;flex-wrap:wrap">
              <button class="btn btn-primary btn-lg" onclick="submitClaim()" style="flex:1">
                📤 Submit Claim
              </button>
              <button class="btn btn-ghost" onclick="clearClaimForm()">🗑️ Clear</button>
            </div>
          </div>
        </div>

        <!-- My Claims -->
        <div class="card">
          <div class="card-header"><h5>📋 My Claims</h5></div>
          <div id="my-claims-list" style="padding:0"></div>
          <div id="empty-claims" class="empty-state" style="padding:30px">
            <div class="empty-icon">📩</div>
            <h4>No Claims Yet</h4>
            <p>Submit your first claim to get started.</p>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- Success Modal -->
  <div class="modal-overlay" id="claim-success-modal">
    <div class="modal" style="max-width:420px;text-align:center">
      <div class="modal-body" style="padding:40px">
        <div style="font-size:64px;margin-bottom:16px">📩</div>
        <h3 style="color:var(--primary);margin-bottom:10px">Claim Submitted!</h3>
        <p id="claim-success-id"
          style="font-size:18px;font-weight:800;color:var(--text-primary);margin:12px 0"></p>
        <p style="font-size:13.5px;color:var(--text-muted);margin-bottom:20px">
          Your claim has been filed and is under review. You will be notified once a decision is made.
        </p>
        <div style="background:#e8f5e9;border-radius:8px;padding:12px;font-size:13px;
          color:var(--primary);margin-bottom:20px">
          ⏳ Status: <strong>Pending Review</strong>
        </div>
        <button class="btn btn-primary w-100"
          onclick="closeModal('claim-success-modal'); loadClaims();">OK</button>
      </div>
    </div>
  </div>

  <?php require_once '../../includes/toast.php'; ?>
  <script>
    initTopbarUser();

    let allClaims      = [];
    let loadedPolicies = [];

    async function loadPolicies() {
      try {
        const res = await api('GET', '/policies?status=active');
        const sel = document.getElementById('claim-app-id');
        if (!sel) return;

        loadedPolicies = (res.success ? (res.data || []) : []).filter(
          p => p.status === 'active' || p.status === 'approved'
        );

        if (!loadedPolicies.length) {
          sel.innerHTML = '<option value="">No approved applications found</option>';
          return;
        }

        sel.innerHTML = '<option value="">Select application...</option>' +
          loadedPolicies.map(p =>
            `<option value="${p.id}">${p.policy_number || 'APP-' + p.id} — ${p.plan_name || 'Insurance Plan'}</option>`
          ).join('');

        // Auto-select policy if ?policy_id= is in URL
        const urlPolicyId = new URLSearchParams(window.location.search).get('policy_id');
        if (urlPolicyId && loadedPolicies.find(p => p.id == urlPolicyId)) {
          sel.value = urlPolicyId;
          populateFromApp();
        }
      } catch (err) {
        console.error('Error loading policies:', err);
      }
    }

    function populateFromApp() {
      const sel         = document.getElementById('claim-app-id');
      const infoBox     = document.getElementById('app-info-box');
      const infoContent = document.getElementById('app-info-content');
      if (!sel || !infoBox || !infoContent) return;

      const policy = loadedPolicies.find(p => p.id == sel.value);
      if (policy) {
        infoContent.innerHTML = `
          <strong>Policy #:</strong> ${policy.policy_number || '—'} &nbsp;|&nbsp;
          <strong>Plan:</strong> ${policy.plan_name || '—'} &nbsp;|&nbsp;
          <strong>Farm:</strong> ${policy.farm_location || policy.barangay || '—'} &nbsp;|&nbsp;
          <strong>Coverage:</strong> ${formatCurrency(policy.coverage_amount || 0)}`;
        infoBox.style.display = 'block';

        const causeEl = document.getElementById('claim-cause');
        if (causeEl && policy.cause_of_damage) causeEl.value = policy.cause_of_damage;

        const percentEl = document.getElementById('claim-percent');
        if (percentEl && policy.percent_damage) percentEl.value = policy.percent_damage;

        const lossDateEl = document.getElementById('claim-loss-date');
        if (lossDateEl && policy.date_of_loss) lossDateEl.value = policy.date_of_loss.split('T')[0];

        const descEl = document.getElementById('claim-description');
        if (descEl && policy.damage_description) descEl.value = policy.damage_description;

        const claimDateEl = document.getElementById('claim-date');
        if (claimDateEl && !claimDateEl.value) claimDateEl.value = new Date().toISOString().split('T')[0];
      } else {
        infoBox.style.display = 'none';
      }
    }

    async function loadClaims() {
      try {
        const res = await api('GET', '/claims');
        if (!res.success) return;
        allClaims = res.data || [];

        const listEl  = document.getElementById('my-claims-list');
        const emptyEl = document.getElementById('empty-claims');
        if (!listEl) return;

        if (!allClaims.length) {
          listEl.innerHTML = '';
          if (emptyEl) emptyEl.style.display = 'block';
          return;
        }
        if (emptyEl) emptyEl.style.display = 'none';

        listEl.innerHTML = allClaims.map(c => `
          <div style="padding:14px 20px;border-bottom:1px solid var(--border-color);
            display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px">
            <div>
              <div style="font-weight:700">${c.claim_number || 'CLM-' + c.id}</div>
              <div style="font-size:12px;color:var(--text-muted)">
                ${c.incident_type || '—'} · ${c.incident_date ? formatDate(c.incident_date) : '—'}
              </div>
            </div>
            <div style="text-align:right">
              <div style="font-size:13px;font-weight:600">${formatCurrency(c.estimated_loss || 0)}</div>
              <div>${getStatusBadge(c.status)}</div>
            </div>
          </div>`).join('');
      } catch (err) {
        console.error('Error loading claims:', err);
      }
    }

    async function uploadClaimDocument(claimId) {
      const fileInput = document.getElementById('claim-photo');
      if (!fileInput || !fileInput.files || !fileInput.files[0]) return;

      const formData = new FormData();
      formData.append('document', fileInput.files[0]);

      try {
        await fetch(API_BASE + '/claims/' + claimId + '/documents', {
          method:  'POST',
          headers: { Authorization: 'Bearer ' + getToken() },
          body:    formData,
        });
      } catch (err) {
        console.error('Document upload failed:', err);
        showToast('Warning', 'Claim submitted but photo upload failed. You can re-upload later.', 'warning');
      }
    }

    async function submitClaim() {
      const get = id => document.getElementById(id)?.value?.trim();

      const policyId      = get('claim-app-id');
      const incidentType  = get('claim-cause');
      const incidentDate  = get('claim-loss-date');
      const description   = get('claim-description');
      const damagePercent = parseFloat(get('claim-percent') || 0);

      if (!policyId)      { showToast('Validation', 'Please select an application.', 'error');      return; }
      if (!incidentType)  { showToast('Validation', 'Please select the cause of damage.', 'error'); return; }
      if (!incidentDate)  { showToast('Validation', 'Please enter the date of loss.', 'error');     return; }
      if (!damagePercent) { showToast('Validation', 'Please enter the damage percentage.', 'error'); return; }

      const policy        = loadedPolicies.find(p => p.id == policyId);
      const estimatedLoss = policy ? (policy.coverage_amount * damagePercent) / 100 : 0;

      showLoading();
      try {
        const res = await api('POST', '/claims', {
          policy_id:         parseInt(policyId),
          incident_type:     incidentType,
          incident_date:     incidentDate,
          description:       description || '',
          estimated_loss:    estimatedLoss,
          damage_percentage: damagePercent,
        });
        hideLoading();

        if (res.success) {
          const claimId = res.data?.id;
          if (claimId) await uploadClaimDocument(claimId);

          document.getElementById('app-info-box').style.display = 'none';
          ['claim-app-id','claim-date','claim-loss-date','claim-cause','claim-percent','claim-description']
            .forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
          document.getElementById('claim-photo-preview').style.display = 'none';

          document.getElementById('claim-success-id').textContent =
            res.data?.claim_number || 'CLM-' + (claimId || '');
          document.getElementById('claim-success-modal').style.display = 'flex';
          loadClaims();
        } else {
          showToast('Error', res.message || 'Failed to file claim.', 'error');
        }
      } catch (err) {
        hideLoading();
        console.error(err);
        showToast('Error', 'Error submitting claim. Please try again.', 'error');
      }
    }

    function clearClaimForm() {
      ['claim-app-id','claim-date','claim-loss-date','claim-cause','claim-percent','claim-description']
        .forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
      document.getElementById('app-info-box').style.display       = 'none';
      document.getElementById('claim-photo-preview').style.display = 'none';
      const fileInput = document.getElementById('claim-photo');
      if (fileInput) fileInput.value = '';
    }

    function previewClaimPhoto(input) {
      if (!input.files || !input.files[0]) return;
      const file    = input.files[0];
      const allowed = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
      if (!allowed.includes(file.type)) {
        showToast('Invalid File', 'Only JPG, PNG, and PDF files are allowed.', 'error');
        input.value = '';
        return;
      }
      if (file.size > 10 * 1024 * 1024) {
        showToast('File Too Large', 'Maximum file size is 10 MB.', 'error');
        input.value = '';
        return;
      }
      if (file.type !== 'application/pdf') {
        const reader = new FileReader();
        reader.onload = e => {
          document.getElementById('claim-preview-img').src = e.target.result;
          document.getElementById('claim-photo-preview').style.display = 'block';
        };
        reader.readAsDataURL(file);
      } else {
        document.getElementById('claim-preview-img').src = '';
        document.getElementById('claim-photo-preview').style.display = 'block';
        document.getElementById('claim-photo-preview').querySelector('p').textContent =
          '✅ PDF selected: ' + file.name;
      }
    }

    loadPolicies();
    loadClaims();
  </script>
</body>
</html>
