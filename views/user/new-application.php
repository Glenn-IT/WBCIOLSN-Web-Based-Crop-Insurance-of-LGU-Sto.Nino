<?php require_once '../../components/under-construction.php'; ?>
<?php
$pageTitle      = 'New Application — Crop Insurance';
$basePath       = '../../';
$currentPage    = 'new-application';
$guardRole      = 'user';
$includeLeaflet = true;
require_once '../../includes/auth-guard.php';
require_once '../../includes/head.php';
?>
<body>
  <div class="app-layout">

    <?php require_once '../../includes/user-sidebar.php'; ?>

    <?php
    $topbarTitle    = 'New Application';
    $topbarSubtitle = 'Fill out all sections to submit your application';
    $isAdmin        = false;
    require_once '../../includes/topbar.php';
    ?>

    <main class="main-content">
      <div class="page-header">
        <div class="page-header-left">
          <div class="breadcrumb">
            <span>Home</span><span class="sep">›</span>
            <span>Applications</span><span class="sep">›</span>
            <span class="current">New Application</span>
          </div>
          <h2>Crop Insurance Application Form</h2>
          <p>Complete all four sections. Fields marked with * are required.</p>
        </div>
      </div>

      <!-- Stepper -->
      <div class="stepper" id="stepper">
        <div class="step active" id="step-1">
          <div class="step-circle">1</div>
          <div class="step-label">Basic Info</div>
        </div>
        <div class="step" id="step-2">
          <div class="step-circle">2</div>
          <div class="step-label">Farm Info</div>
        </div>
        <div class="step" id="step-3">
          <div class="step-circle">3</div>
          <div class="step-label">Damage Report</div>
        </div>
        <div class="step" id="step-4">
          <div class="step-circle">4</div>
          <div class="step-label">Coverage &amp; Consent</div>
        </div>
      </div>

      <!-- Step 1 -->
      <div id="content-step-1" class="step-content">
        <div class="card">
          <div class="card-header">
            <h5>📋 Step 1: Basic Information</h5>
            <span class="badge badge-primary">Step 1 of 4</span>
          </div>
          <div class="card-body">
            <div class="form-section-title">👤 Applicant Details</div>
            <div class="form-row form-row-2">
              <div class="form-group">
                <label class="form-label">Application Type *</label>
                <select id="applicationType" class="form-select">
                  <option value="">Select type</option>
                  <option value="New">New</option>
                  <option value="Renewal">Renewal</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Date of Application *</label>
                <input type="date" id="dateOfApplication" class="form-control" />
              </div>
            </div>
            <div class="form-row form-row-2">
              <div class="form-group">
                <label class="form-label">Farmer Category *</label>
                <select id="farmerCategory" class="form-select">
                  <option value="">Select category</option>
                  <option>Small Farmer</option>
                  <option>Commercial Farmer</option>
                  <option>Agrarian Reform Beneficiary</option>
                  <option>Fisherfolk</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Crop Type *</label>
                <select id="cropTypeId" class="form-select">
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
              <input type="text" id="farmerName" class="form-control"
                placeholder="First Middle Last"
                value="<?= htmlspecialchars(($authUser['first_name'] ?? '') . ' ' . ($authUser['last_name'] ?? '')) ?>" />
            </div>
          </div>
        </div>
        <div style="display:flex;justify-content:flex-end;margin-top:16px">
          <button class="btn btn-primary btn-lg" onclick="nextStep()">
            Next: Farm Information →
          </button>
        </div>
      </div>

      <!-- Step 2 -->
      <div id="content-step-2" class="step-content" style="display:none">
        <div class="card">
          <div class="card-header">
            <h5>🌾 Step 2: Farm Information</h5>
            <span class="badge badge-primary">Step 2 of 4</span>
          </div>
          <div class="card-body">
            <div class="form-section-title">📍 Location &amp; Area</div>
            <div class="form-group">
              <label class="form-label">Farm Location *</label>
              <input type="text" id="farmLocation" class="form-control"
                placeholder="Barangay, Municipality, Province" />
            </div>
            <div class="form-row form-row-3">
              <div class="form-group">
                <label class="form-label">Total Area (hectares) *</label>
                <input type="number" id="totalArea" class="form-control"
                  placeholder="e.g. 2.5" step="0.1" />
              </div>
              <div class="form-group">
                <label class="form-label">Land Category *</label>
                <select id="landCategory" class="form-select">
                  <option value="">Select</option>
                  <option>Irrigated</option>
                  <option>Rain-fed</option>
                  <option>Upland</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Tenurial Status *</label>
                <select id="tenuralStatus" class="form-select">
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
                <input type="date" id="plantingDate" class="form-control" />
              </div>
              <div class="form-group">
                <label class="form-label">Expected Harvest Date *</label>
                <input type="date" id="harvestDate" class="form-control" />
              </div>
              <div class="form-group">
                <label class="form-label">Planting Method *</label>
                <select id="plantingMethod" class="form-select">
                  <option value="">Select</option>
                  <option>Direct Seeding</option>
                  <option>Transplanting</option>
                  <option>Broadcast</option>
                </select>
              </div>
            </div>
            <div class="form-section-title" style="margin-top:20px">📌 Geo Tag Location
              <span style="font-size:11px;font-weight:400;color:var(--text-muted);margin-left:8px">(Recommended — click anywhere on the map to pin your farm)</span>
            </div>
            <div id="farm-map" style="height:320px;border-radius:10px;border:1px solid var(--border-color);
              margin-top:8px;z-index:0"></div>
            <div id="geo-coords" style="display:none;margin-top:8px;padding:8px 12px;
              background:#e8f5e9;border-radius:8px;font-size:13px;color:#2e7d32">
              📍 Pinned: <strong id="geo-lat-display"></strong>, <strong id="geo-lng-display"></strong>
              <button type="button" onclick="clearGeoTag()"
                style="margin-left:12px;font-size:11px;color:#c62828;background:none;border:none;
                  cursor:pointer;text-decoration:underline">Remove pin</button>
            </div>
            <p style="font-size:11.5px;color:var(--text-muted);margin-top:6px">
              💡 Powered by OpenStreetMap — free, no sign-up required.
            </p>
            <input type="hidden" id="geoLat" value="" />
            <input type="hidden" id="geoLng" value="" />
          </div>
        </div>
        <div style="display:flex;justify-content:space-between;margin-top:16px">
          <button class="btn btn-ghost btn-lg" onclick="prevStep()">← Back</button>
          <button class="btn btn-primary btn-lg" onclick="nextStep()">Next: Damage Report →</button>
        </div>
      </div>

      <!-- Step 3 -->
      <div id="content-step-3" class="step-content" style="display:none">
        <div class="card">
          <div class="card-header">
            <h5>⚠️ Step 3: Damage Report</h5>
            <span class="badge badge-primary">Step 3 of 4</span>
          </div>
          <div class="card-body">
            <div class="form-section-title">🌪️ Damage Details</div>
            <div class="form-row form-row-2">
              <div class="form-group">
                <label class="form-label">Date of Loss *</label>
                <input type="date" id="dateOfLoss" class="form-control" />
              </div>
              <div class="form-group">
                <label class="form-label">Cause of Damage *</label>
                <select id="causeOfDamage" class="form-select">
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
                <label class="form-label">Percentage of Damage (%) *
                  <span style="font-size:11px;font-weight:400;color:var(--text-muted)">
                    — visually assessed % of crop lost
                  </span>
                </label>
                <input type="number" id="percentDamage" class="form-control"
                  placeholder="e.g. 75" min="1" max="100"
                  oninput="calcFinancialDamage()" />
              </div>
              <div class="form-group">
                <label class="form-label">Financial Damage (₱) *
                  <span style="font-size:11px;font-weight:400;color:var(--text-muted)">
                    — estimated peso value of loss
                  </span>
                </label>
                <input type="number" id="financialDamage" class="form-control"
                  placeholder="Auto-calculated or enter manually"
                  oninput="financialDamageManual = true" />
                <small id="financial-damage-hint"
                  style="display:none;font-size:11.5px;color:#1565c0;margin-top:4px">
                </small>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Description of Damage</label>
              <textarea id="damageDescription" class="form-control"
                placeholder="Describe the extent of damage in detail..."></textarea>
            </div>
            <div class="form-section-title" style="margin-top:20px">
              📸 Photo Evidence
              <span id="photo-count-badge"
                style="margin-left:8px;font-size:12px;font-weight:400;color:var(--text-muted)">
                0 / min 5 photos
              </span>
            </div>
            <div class="file-upload-area" id="photo-drop-zone"
              onclick="document.getElementById('damagePhotos').click()"
              ondragover="event.preventDefault()" ondrop="handlePhotoDrop(event)"
              style="cursor:pointer">
              <input type="file" id="damagePhotos" accept="image/png,image/jpeg,image/jpg"
                multiple style="display:none" onchange="addPhotos(this)" />
              <div class="file-upload-icon">📸</div>
              <p class="file-upload-text"><span>Click to add photos</span> or drag &amp; drop</p>
              <p class="file-upload-text">PNG, JPG, JPEG — up to 10 MB each — minimum 5 photos required</p>
            </div>
            <div id="photo-error-msg"
              style="display:none;color:#dc3545;font-size:12.5px;margin-top:6px;padding:6px 10px;
                background:#fde8e8;border-radius:6px">
              ⚠️ Please add at least 5 photos as evidence of damage.
            </div>
            <div id="photo-grid"
              style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));
                gap:10px;margin-top:12px"></div>
          </div>
        </div>
        <div style="display:flex;justify-content:space-between;margin-top:16px">
          <button class="btn btn-ghost btn-lg" onclick="prevStep()">← Back</button>
          <button class="btn btn-primary btn-lg" onclick="nextStep()">Next: Coverage &amp; Consent →</button>
        </div>
      </div>

      <!-- Step 4 -->
      <div id="content-step-4" class="step-content" style="display:none">
        <div class="card">
          <div class="card-header">
            <h5>🔒 Step 4: Coverage &amp; Consent</h5>
            <span class="badge badge-primary">Step 4 of 4</span>
          </div>
          <div class="card-body">
            <div class="form-section-title">💰 Coverage Plan</div>
            <div class="form-row form-row-2">
              <div class="form-group">
                <label class="form-label">Select Coverage Plan *</label>
                <select id="planId" class="form-select" onchange="onPlanChange()">
                  <option value="">Loading plans...</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Desired Coverage Amount (₱) *</label>
                <input type="number" id="desiredCoverage" class="form-control" placeholder="e.g. 50000" />
              </div>
            </div>
            <div id="plan-info" style="display:none;background:#f0faf4;border:1px solid #a5d6a7;
              border-radius:10px;padding:14px 18px;margin-bottom:4px;font-size:13px">
              <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
                <div>
                  <div style="color:var(--text-muted);font-weight:600;margin-bottom:2px">Coverage Type</div>
                  <div id="plan-info-type" style="font-weight:700;color:var(--primary)">—</div>
                </div>
                <div>
                  <div style="color:var(--text-muted);font-weight:600;margin-bottom:2px">Coverage %</div>
                  <div id="plan-info-pct" style="font-weight:700">—</div>
                </div>
                <div>
                  <div style="color:var(--text-muted);font-weight:600;margin-bottom:2px">Max Coverage</div>
                  <div id="plan-info-max" style="font-weight:700;color:var(--success)">—</div>
                </div>
              </div>
            </div>
            <div class="form-section-title" style="margin-top:20px">📄 Documents</div>
            <div class="form-group">
              <label class="form-label">Valid ID Upload</label>
              <div class="file-upload-area" style="padding:18px;cursor:pointer;max-width:400px"
                onclick="document.getElementById('id-file').click()">
                <input type="file" id="id-file" accept="image/*,.pdf" style="display:none"
                  onchange="showFileName(this,'id-label')" />
                <div class="file-upload-icon" style="font-size:22px">🪪</div>
                <p class="file-upload-text"><span>Upload Valid ID</span></p>
                <p class="file-upload-text" style="font-size:11.5px">Image or PDF accepted</p>
                <p id="id-label" style="font-size:12px;color:var(--primary);margin-top:4px"></p>
              </div>
            </div>
            <div class="form-section-title" style="margin-top:20px">☑️ Declaration &amp; Consent</div>
            <div style="background:#f8fafc;border-radius:10px;padding:20px;border:1px solid var(--border-color);
              margin-bottom:16px;font-size:13px;color:var(--text-secondary);line-height:1.7">
              I hereby declare that all information provided in this application is true, accurate, and complete.
              I understand that any false information may result in the denial or cancellation of my insurance
              coverage. I consent to the LGU verification of my farm and damage reports.
            </div>
            <div class="form-group">
              <label class="form-check">
                <input type="checkbox" id="consent-check" />
                <span style="font-size:13.5px">I have read and agree to the
                  <strong>Terms and Conditions</strong> of the LGU Sto. Niño Crop Insurance Program.</span>
              </label>
            </div>
            <div class="form-group">
              <label class="form-check">
                <input type="checkbox" id="accuracy-check" />
                <span style="font-size:13.5px">I confirm that all information provided is accurate and truthful.</span>
              </label>
            </div>
          </div>
        </div>
        <div style="display:flex;justify-content:space-between;margin-top:16px">
          <button class="btn btn-ghost btn-lg" onclick="prevStep()">← Back</button>
          <div style="display:flex;gap:10px">
            <button class="btn btn-outline btn-lg" onclick="clearForm()">🗑️ Clear Form</button>
            <button class="btn btn-primary btn-lg" id="submit-btn" onclick="submitApplication()">
              📤 Submit Application
            </button>
          </div>
        </div>
      </div>

    </main>
  </div>

  <?php require_once '../../includes/toast.php'; ?>
  <script>
    initTopbarUser();

    var TOTAL_STEPS  = 4;
    var currentStep  = 1;
    var isSubmitting = false;
    var availablePlans = [];

    // ── Leaflet map state ──────────────────────────────────────────────
    var farmMap      = null;
    var farmMarker   = null;
    var mapInitialized = false;

    function initFarmMap() {
      if (mapInitialized) {
        farmMap.invalidateSize();
        return;
      }
      mapInitialized = true;

      // Default center: Sto. Niño, South Cotabato, Philippines
      farmMap = L.map('farm-map').setView([6.4573, 124.8340], 13);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
      }).addTo(farmMap);

      farmMap.on('click', function(e) {
        var lat = e.latlng.lat.toFixed(6);
        var lng = e.latlng.lng.toFixed(6);
        setGeoPin(lat, lng);
      });
    }

    function setGeoPin(lat, lng) {
      if (farmMarker) farmMap.removeLayer(farmMarker);
      farmMarker = L.marker([lat, lng]).addTo(farmMap)
        .bindPopup('📍 Farm Location<br><small>' + lat + ', ' + lng + '</small>')
        .openPopup();
      document.getElementById('geoLat').value          = lat;
      document.getElementById('geoLng').value          = lng;
      document.getElementById('geo-lat-display').textContent = lat;
      document.getElementById('geo-lng-display').textContent = lng;
      document.getElementById('geo-coords').style.display   = 'block';
    }

    function clearGeoTag() {
      if (farmMarker) { farmMap.removeLayer(farmMarker); farmMarker = null; }
      document.getElementById('geoLat').value               = '';
      document.getElementById('geoLng').value               = '';
      document.getElementById('geo-coords').style.display   = 'none';
    }
    // ──────────────────────────────────────────────────────────────────

    function showStep(step) {
      if (step < 1 || step > TOTAL_STEPS) return;
      for (var i = 1; i <= TOTAL_STEPS; i++) {
        var c = document.getElementById('content-step-' + i);
        var s = document.getElementById('step-' + i);
        if (c) c.style.display = i === step ? 'block' : 'none';
        if (s) {
          s.classList.toggle('active',    i === step);
          s.classList.toggle('completed', i < step);
        }
      }
      currentStep = step;
      window.scrollTo({ top: 0, behavior: 'smooth' });
      // Initialize or resize map when step 2 becomes visible
      if (step === 2) {
        setTimeout(initFarmMap, 50);
      }
    }

    async function loadPlans() {
      const sel = document.getElementById('planId');
      if (!sel || availablePlans.length > 0) return; // already loaded
      try {
        const res = await api('GET', '/plans');
        availablePlans = res.success && Array.isArray(res.data) ? res.data : [];
        if (availablePlans.length === 0) {
          sel.innerHTML = '<option value="">No plans available</option>';
          return;
        }
        sel.innerHTML = '<option value="">Select a plan...</option>' +
          availablePlans.map(p =>
            `<option value="${p.id}">${p.plan_name} — max ${formatCurrency(p.max_coverage_amount)}</option>`
          ).join('');
      } catch (err) {
        sel.innerHTML = '<option value="">Failed to load plans</option>';
        console.error('loadPlans error:', err);
      }
    }

    function onPlanChange() {
      const planId = document.getElementById('planId')?.value;
      const plan   = availablePlans.find(p => p.id == planId);
      const info   = document.getElementById('plan-info');
      if (!plan || !info) { if (info) info.style.display = 'none'; return; }
      info.style.display = 'block';
      document.getElementById('plan-info-type').textContent = (plan.coverage_type || '—').replace(/_/g, ' ');
      document.getElementById('plan-info-pct').textContent  = (plan.coverage_percent ?? '—') + '%';
      document.getElementById('plan-info-max').textContent  = formatCurrency(plan.max_coverage_amount ?? 0);
      const cov = document.getElementById('desiredCoverage');
      if (cov && !cov.value) cov.value = plan.max_coverage_amount || '';
    }

    function prevStep() { if (currentStep > 1) showStep(currentStep - 1); }

    function nextStep() {
      if (currentStep === 1) {
        if (!document.getElementById('applicationType').value) {
          showToast('Validation', 'Please select Application Type.', 'error'); return;
        }
        if (!document.getElementById('farmerName').value.trim()) {
          showToast('Validation', 'Please enter the Farmer Full Name.', 'error'); return;
        }
        if (!document.getElementById('cropTypeId').value) {
          showToast('Validation', 'Please select a Crop Type.', 'error'); return;
        }
      }
      if (currentStep === 2) {
        if (!document.getElementById('farmLocation').value.trim()) {
          showToast('Validation', 'Please enter Farm Location.', 'error'); return;
        }
        var area = parseFloat(document.getElementById('totalArea').value);
        if (!area || area <= 0) {
          showToast('Validation', 'Please enter a valid Total Area.', 'error'); return;
        }
      }
      if (currentStep === 3) {
        if (!document.getElementById('causeOfDamage').value) {
          showToast('Validation', 'Please select Cause of Damage.', 'error'); return;
        }
        if (selectedPhotos.length < 5) {
          var err = document.getElementById('photo-error-msg');
          if (err) err.style.display = 'block';
          showToast('Photos Required', 'Please add at least 5 photos as evidence of damage.', 'error');
          return;
        }
        showStep(4);
        loadPlans();
        return;
      }
      showStep(currentStep + 1);
    }

    function showFileName(input, labelId) {
      var label = document.getElementById(labelId);
      if (label && input.files && input.files[0]) {
        label.textContent = '✅ ' + input.files[0].name;
      }
    }

    // ── Multi-photo state ──────────────────────────────────────────────
    var selectedPhotos = []; // array of File objects

    function addPhotos(input) {
      var files = Array.from(input.files || []);
      var rejected = [];
      files.forEach(function(file) {
        // type check
        if (!['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
          rejected.push(file.name + ' (not an image)'); return;
        }
        // size check (10 MB)
        if (file.size > 10 * 1024 * 1024) {
          rejected.push(file.name + ' (exceeds 10 MB)'); return;
        }
        // deduplicate by name + size
        var dup = selectedPhotos.some(function(f) {
          return f.name === file.name && f.size === file.size;
        });
        if (!dup) selectedPhotos.push(file);
      });
      if (rejected.length) {
        showToast('Skipped', rejected.join(', '), 'warning');
      }
      input.value = ''; // reset so same file can be re-added if removed
      renderPhotoGrid();
    }

    function handlePhotoDrop(event) {
      event.preventDefault();
      var dt = event.dataTransfer;
      if (!dt || !dt.files) return;
      // simulate adding via a temporary array
      var fakeInput = { files: dt.files };
      addPhotos(fakeInput);
    }

    function removePhoto(index) {
      selectedPhotos.splice(index, 1);
      renderPhotoGrid();
    }

    function renderPhotoGrid() {
      var grid  = document.getElementById('photo-grid');
      var badge = document.getElementById('photo-count-badge');
      var err   = document.getElementById('photo-error-msg');
      if (!grid) return;

      var count = selectedPhotos.length;
      if (badge) {
        badge.textContent = count + ' / min 5 photos';
        badge.style.color = count >= 5 ? '#2e7d32' : 'var(--text-muted)';
      }
      if (err && count >= 5) err.style.display = 'none';

      if (count === 0) { grid.innerHTML = ''; return; }

      grid.innerHTML = selectedPhotos.map(function(file, i) {
        return '<div id="thumb-wrap-' + i + '" style="position:relative;border-radius:8px;'
          + 'overflow:hidden;border:1px solid var(--border-color);aspect-ratio:1">'
          + '<img id="thumb-' + i + '" src="" style="width:100%;height:100%;object-fit:cover" />'
          + '<button type="button" onclick="removePhoto(' + i + ')" '
          + 'style="position:absolute;top:4px;right:4px;background:rgba(0,0,0,0.6);color:#fff;'
          + 'border:none;border-radius:50%;width:22px;height:22px;font-size:12px;'
          + 'cursor:pointer;line-height:22px;text-align:center;padding:0">×</button>'
          + '<div style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,0.5);'
          + 'color:#fff;font-size:10px;padding:2px 4px;white-space:nowrap;overflow:hidden;'
          + 'text-overflow:ellipsis">' + (i + 1) + '. ' + file.name + '</div>'
          + '</div>';
      }).join('');

      // Load thumbnails asynchronously
      selectedPhotos.forEach(function(file, i) {
        var reader = new FileReader();
        reader.onload = function(e) {
          var img = document.getElementById('thumb-' + i);
          if (img) img.src = e.target.result;
        };
        reader.readAsDataURL(file);
      });

      // Show "Add more" prompt after grid
      grid.insertAdjacentHTML('afterend', ''); // placeholder already handled by onclick on drop-zone
    }
    // ──────────────────────────────────────────────────────────────────

    // ── PCIC Damage Auto-Calculator ────────────────────────────────────
    // Average gross value per hectare per crop (PH 2024 reference, PCIC basis)
    var CROP_VALUE_PER_HA = {
      1:  84000,   // Rice       — 70 bags/ha × 50 kg × ₱24/kg
      2:  49000,   // Corn       — 3,500 kg/ha × ₱14/kg
      3: 162500,   // Sugarcane  — 65,000 kg/ha × ₱2.5/kg
      4: 300000,   // Banana     — 20,000 kg/ha × ₱15/kg
      5:  40000,   // Coconut    — 5,000 nuts/ha × ₱8/nut
      6: 250000,   // Vegetables — 10,000 kg/ha × ₱25/kg
      7:  80000,   // Cassava    — 20,000 kg/ha × ₱4/kg
      8: 100000,   // Coffee     — 500 kg/ha × ₱200/kg
      9: 280000,   // Mango      — 8,000 kg/ha × ₱35/kg
      10: 96000,   // Tobacco    — 1,200 kg/ha × ₱80/kg
    };

    var CROP_NAMES = {
      1:'Rice',2:'Corn',3:'Sugarcane',4:'Banana',5:'Coconut',
      6:'Vegetables',7:'Cassava',8:'Coffee',9:'Mango',10:'Tobacco'
    };

    var financialDamageManual = false; // true once user hand-edits the field

    function calcFinancialDamage() {
      var pct      = parseFloat(document.getElementById('percentDamage').value)  || 0;
      var area     = parseFloat(document.getElementById('totalArea').value)       || 0;
      var cropId   = parseInt(document.getElementById('cropTypeId').value)        || 0;
      var hint     = document.getElementById('financial-damage-hint');
      var fdInput  = document.getElementById('financialDamage');

      if (pct <= 0 || pct > 100 || area <= 0 || !cropId || !CROP_VALUE_PER_HA[cropId]) {
        if (hint) hint.style.display = 'none';
        return;
      }

      var valuePerHa = CROP_VALUE_PER_HA[cropId];
      var estimated  = Math.round(area * valuePerHa * (pct / 100));

      // Only auto-fill if user hasn't manually typed a value
      if (!financialDamageManual) {
        fdInput.value = estimated;
      }

      if (hint) {
        hint.style.display = 'block';
        var cropName = CROP_NAMES[cropId] || 'Crop';
        hint.innerHTML =
          '💡 Auto-calculated: ' + area + ' ha × ₱' + valuePerHa.toLocaleString()
          + '/ha (' + cropName + ') × ' + pct + '% = <strong>₱'
          + estimated.toLocaleString() + '</strong>'
          + (financialDamageManual
              ? ' &nbsp;<a href="#" onclick="event.preventDefault();financialDamageManual=false;'
                + 'calcFinancialDamage()" style="font-size:11px">Reset to auto</a>'
              : '');
      }
    }
    // ──────────────────────────────────────────────────────────────────

    function clearForm() {
      if (!confirm('Clear all form data and start over?')) return;
      document.querySelectorAll('input:not([type=file]):not([type=checkbox]),select,textarea')
        .forEach(function(el) { el.value = ''; });
      document.querySelectorAll('input[type=checkbox]')
        .forEach(function(el) { el.checked = false; });
      selectedPhotos = [];
      renderPhotoGrid();
      financialDamageManual = false;
      var photoErr = document.getElementById('photo-error-msg');
      if (photoErr) photoErr.style.display = 'none';
      var hint = document.getElementById('financial-damage-hint');
      if (hint) hint.style.display = 'none';
      clearGeoTag();
      showStep(1);
      showToast('Form Cleared', 'All fields have been reset.', 'info');
    }

    function resetSubmitBtn() {
      isSubmitting = false;
      var btn = document.getElementById('submit-btn');
      if (btn) { btn.disabled = false; btn.textContent = '📤 Submit Application'; }
    }

    async function submitApplication() {
      if (isSubmitting) {
        showToast('Please wait', 'Your application is already being submitted.', 'info');
        return;
      }
      if (!document.getElementById('consent-check').checked || !document.getElementById('accuracy-check').checked) {
        showToast('Consent Required', 'Please check both declaration boxes.', 'error');
        return;
      }

      function g(id) { var el = document.getElementById(id); return el ? el.value.trim() : ''; }

      var cropTypeId = parseInt(g('cropTypeId'));
      if (!cropTypeId) {
        showToast('Missing', 'Please select a Crop Type.', 'error');
        showStep(1); return;
      }

      var selectedPlanId = parseInt(g('planId'));
      if (!selectedPlanId) {
        showToast('Missing', 'Please select a Coverage Plan.', 'error');
        return;
      }
      var selectedPlan = availablePlans.find(function(p) { return p.id == selectedPlanId; });
      if (!selectedPlan) {
        showToast('Error', 'Selected plan is invalid. Please reload and try again.', 'error');
        return;
      }

      var geoLat = parseFloat(document.getElementById('geoLat').value) || null;
      var geoLng = parseFloat(document.getElementById('geoLng').value) || null;

      var farmData = {
        farm_name:        (g('farmerName') || 'My') + ' Farm',
        application_type: g('applicationType'),
        farmer_category:  g('farmerCategory'),
        location:         g('farmLocation'),
        area_hectares:    parseFloat(g('totalArea')) || 0,
        crop_type_id:     cropTypeId,
        soil_type:        g('landCategory'),
        tenurial_status:  g('tenuralStatus'),
        planting_method:  g('plantingMethod'),
        planting_date:    g('plantingDate'),
        harvest_date:     g('harvestDate'),
        irrigation:       0,
        latitude:         geoLat,
        longitude:        geoLng,
      };
      if (!farmData.location) { showToast('Missing', 'Farm Location is required.', 'error'); showStep(2); return; }
      if (!farmData.area_hectares) { showToast('Missing', 'Total Area is required.', 'error'); showStep(2); return; }

      var btn = document.getElementById('submit-btn');
      if (btn) { btn.disabled = true; btn.textContent = '⏳ Submitting...'; }
      isSubmitting = true;
      showLoading();

      try {
        var farmRes = await api('POST', '/farms', farmData);
        if (!farmRes.success || !farmRes.data) {
          showToast('Error', farmRes.message || 'Failed to save farm.', 'error');
          hideLoading(); resetSubmitBtn(); return;
        }

        var startDate       = g('plantingDate') || new Date().toISOString().split('T')[0];
        var desiredCoverage = parseFloat(g('desiredCoverage')) || parseFloat(selectedPlan.max_coverage_amount) || null;
        var polRes = await api('POST', '/policies', {
          farm_id:            farmRes.data.id,
          plan_id:            selectedPlan.id,
          start_date:         startDate,
          coverage_amount:    desiredCoverage,
          cause_of_damage:    g('causeOfDamage'),
          percent_damage:     parseFloat(g('percentDamage'))   || null,
          financial_damage:   parseFloat(g('financialDamage')) || null,
          damage_description: g('damageDescription'),
          date_of_loss:       g('dateOfLoss'),
        });

        hideLoading(); resetSubmitBtn();

        if (!polRes.success) {
          // 409 means a pending/active policy already exists for this farm — treat as success
          if (polRes.status === 409 && polRes.message) {
            showToast('Notice', polRes.message, 'warning');
            setTimeout(function() { navigateTo('my-applications.php'); }, 2200);
          } else {
            showToast('Error', polRes.message || 'Failed to submit application.', 'error');
          }
          return;
        }

        var pol = polRes.data || {};
        var policyRef = pol.policy_number || pol.id || 'N/A';

        // Upload damage photos
        var photosFailed = 0;
        if (selectedPhotos.length > 0 && pol.id) {
          showLoading();
          var uploaded = 0;
          for (var pi = 0; pi < selectedPhotos.length; pi++) {
            try {
              var fd = new FormData();
              fd.append('document', selectedPhotos[pi]);
              fd.append('type', 'damage_photo');
              var upRes = await fetch(API_BASE + '/policies/' + pol.id + '/documents', {
                method: 'POST',
                headers: { Authorization: 'Bearer ' + getToken() },
                body: fd,
              });
              if (upRes.ok) uploaded++; else photosFailed++;
            } catch (upErr) {
              photosFailed++;
            }
          }
          hideLoading();
        }

        // Upload valid ID
        var idFile = document.getElementById('id-file');
        var idFailed = false;
        if (idFile && idFile.files.length > 0 && pol.id) {
          showLoading();
          try {
            var idFd = new FormData();
            idFd.append('document', idFile.files[0]);
            idFd.append('type', 'valid_id');
            var idRes = await fetch(API_BASE + '/policies/' + pol.id + '/documents', {
              method: 'POST',
              headers: { Authorization: 'Bearer ' + getToken() },
              body: idFd,
            });
            if (!idRes.ok) idFailed = true;
          } catch (idErr) {
            idFailed = true;
          }
          hideLoading();
        }

        // Upload result feedback
        if (photosFailed > 0 || idFailed) {
          var msg = [];
          if (photosFailed > 0) msg.push(photosFailed + ' photo(s) failed to upload');
          if (idFailed) msg.push('Valid ID upload failed');
          showToast('Upload Warning', msg.join(' · ') + ' — you can re-upload from My Applications.', 'warning');
        }

        showToast('Success', 'Application submitted! ID: ' + policyRef, 'success');
        setTimeout(function() { navigateTo('my-applications.php'); }, 1800);

      } catch (err) {
        hideLoading(); resetSubmitBtn();
        showToast('Error', 'Submission failed: ' + err.message, 'error');
      }
    }

    showStep(1);
  </script>
</body>
</html>
