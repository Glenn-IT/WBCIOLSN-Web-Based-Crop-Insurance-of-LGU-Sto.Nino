<?php
$pageTitle   = 'Application Status — Crop Insurance';
$basePath    = '../../';
$currentPage = 'application-status';
$guardRole   = 'user';
require_once '../../includes/auth-guard.php';
require_once '../../includes/head.php';
?>
<body>
  <div class="app-layout">

    <?php require_once '../../includes/user-sidebar.php'; ?>

    <?php
    $topbarTitle    = 'Application Status';
    $topbarSubtitle = 'Track the progress of your applications';
    $isAdmin        = false;
    require_once '../../includes/topbar.php';
    ?>

    <main class="main-content">
      <div class="page-header">
        <div class="page-header-left">
          <div class="breadcrumb">
            <span>Home</span><span class="sep">›</span>
            <span class="current">Application Status</span>
          </div>
          <h2>Application Status Tracker</h2>
          <p>Monitor your applications and their verification progress</p>
        </div>
      </div>

      <!-- ── DETAIL VIEW (shown when ?id= is in URL) ─────────────── -->
      <div id="detail-view" style="display:none">
        <div style="margin-bottom:16px">
          <button class="btn btn-ghost btn-sm" onclick="navigateTo('application-status.php')">← Back to All Applications</button>
        </div>

        <!-- Policy header card -->
        <div class="card" style="margin-bottom:24px">
          <div id="detail-header" style="background:linear-gradient(135deg,var(--primary-dark),var(--primary));
            padding:24px 28px;border-radius:12px 12px 0 0;color:white;
            display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
            <div>
              <div style="font-size:11px;opacity:0.75;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px">Policy Number</div>
              <div id="detail-policy-num" style="font-size:22px;font-weight:800">—</div>
              <div id="detail-plan" style="font-size:13px;opacity:0.8;margin-top:4px">—</div>
            </div>
            <div id="detail-status-badge"></div>
          </div>
          <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px">
              <div style="background:#f8fafc;border-radius:8px;padding:12px 14px">
                <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px">Coverage Amount</div>
                <div id="detail-coverage" style="font-weight:700;color:var(--primary);font-size:16px">—</div>
              </div>
              <div style="background:#f8fafc;border-radius:8px;padding:12px 14px">
                <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px">Crop Type</div>
                <div id="detail-crop" style="font-weight:600;font-size:14px">—</div>
              </div>
              <div style="background:#f8fafc;border-radius:8px;padding:12px 14px">
                <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px">Farm Location</div>
                <div id="detail-location" style="font-weight:600;font-size:13px">—</div>
              </div>
              <div style="background:#f8fafc;border-radius:8px;padding:12px 14px">
                <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px">Date Filed</div>
                <div id="detail-filed" style="font-weight:600;font-size:13px">—</div>
              </div>
            </div>
            <div id="detail-remarks" style="display:none;margin-top:14px;padding:10px 14px;
              background:#fff3cd;border-radius:8px;font-size:13px;color:#856404;border:1px solid #ffc107"></div>
          </div>
        </div>

        <!-- 5-Stage verification timeline -->
        <div class="card">
          <div class="card-header"><h5>🔎 Verification Timeline</h5></div>
          <div class="card-body" style="padding:28px">
            <div id="timeline-stages" style="position:relative"></div>
          </div>
        </div>
      </div>

      <!-- ── LIST VIEW (default, no ?id=) ──────────────────────── -->
      <div id="list-view">
        <!-- Filter Tabs -->
        <div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap">
          <button class="btn btn-primary" onclick="filterStatus('')"             id="tab-all">All</button>
          <button class="btn btn-ghost"   onclick="filterStatus('pending')"      id="tab-pending">⏳ Pending</button>
          <button class="btn btn-ghost"   onclick="filterStatus('under_review')" id="tab-under_review">🔍 Under Review</button>
          <button class="btn btn-ghost"   onclick="filterStatus('active')"       id="tab-active">✅ Approved</button>
          <button class="btn btn-ghost"   onclick="filterStatus('rejected')"     id="tab-rejected">❌ Rejected</button>
        </div>

        <!-- Application Cards -->
        <div id="status-cards"
          style="display:grid;grid-template-columns:repeat(auto-fill,minmax(360px,1fr));gap:20px"></div>
        <div id="empty-status" class="empty-state hidden">
          <div class="empty-icon">🔍</div>
          <h4>No Applications Found</h4>
          <p>No applications match your filter.</p>
        </div>
      </div>
    </main>
  </div>

  <?php require_once '../../includes/toast.php'; ?>
  <script>
    initTopbarUser();

    let allPolicies  = [];
    let activeFilter = '';

    const CROP_ICON = {
      Rice: '🌾', Corn: '🌽', Banana: '🍌', Coconut: '🥥',
      Mango: '🥭', Sugarcane: '🎋', Vegetables: '🥦',
      Cassava: '🍠', Coffee: '☕', Tobacco: '🌿',
    };

    // ── DETAIL VIEW ─────────────────────────────────────────────

    function renderTimeline(p) {
      const isRejected = ['rejected', 'cancelled'].includes((p.status || '').toLowerCase());
      const isApproved = p.status === 'active';

      const stages = [
        {
          label: 'Application Submitted',
          icon:  '📋',
          ts:    p.created_at,
          desc:  'Your application has been received by the LGU.',
          done:  true,
        },
        {
          label: 'Farm Verification',
          icon:  '🌾',
          ts:    p.farm_verified_at || null,
          desc:  'A field officer will verify your farm details and location.',
          done:  p.farm_verification === 'Verified',
        },
        {
          label: 'Damage Report Review',
          icon:  '⚠️',
          ts:    p.damage_reviewed_at || null,
          desc:  'The damage report and supporting documents are being assessed.',
          done:  p.damage_verification === 'Verified',
        },
        {
          label: 'Coverage Finalization',
          icon:  '🔒',
          ts:    p.coverage_set_at || null,
          desc:  'Coverage amount and premium are being finalized.',
          done:  p.coverage_verification === 'Verified',
        },
        {
          label: isRejected ? 'Application Rejected' : 'Final Approval',
          icon:  isRejected ? '❌' : '✅',
          ts:    p.approved_at || p.rejected_at || p.final_decision_at,
          desc:  isRejected
            ? (p.rejection_reason || 'Your application was not approved.')
            : isApproved
              ? 'Your application has been approved. Coverage is now active.'
              : 'Awaiting final decision from the LGU.',
          done:  isApproved || isRejected,
        },
      ];

      return stages.map((s, i) => {
        const isLast   = i === stages.length - 1;
        const dotColor = s.done
          ? (isLast && isRejected ? '#dc3545' : 'var(--primary)')
          : '#cbd5e1';
        const lineColor = s.done && !isLast ? 'var(--primary)' : '#e2e8f0';

        return `
          <div style="display:flex;gap:16px;margin-bottom:${isLast ? '0' : '0'}">
            <div style="display:flex;flex-direction:column;align-items:center;min-width:36px">
              <div style="width:36px;height:36px;border-radius:50%;background:${dotColor};
                display:flex;align-items:center;justify-content:center;font-size:16px;
                flex-shrink:0;box-shadow:0 2px 6px rgba(0,0,0,0.12);transition:background 0.3s">
                ${s.done ? s.icon : '<span style="font-size:11px;color:#94a3b8;font-weight:700">' + (i + 1) + '</span>'}
              </div>
              ${!isLast ? `<div style="width:2px;flex:1;min-height:28px;background:${lineColor};margin:4px 0;transition:background 0.3s"></div>` : ''}
            </div>
            <div style="padding-bottom:${isLast ? '0' : '24px'};flex:1">
              <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;flex-wrap:wrap">
                <div style="font-weight:${s.done ? '700' : '500'};font-size:14px;
                  color:${s.done ? (isLast && isRejected ? '#dc3545' : 'var(--primary)') : 'var(--text-muted)'}">
                  ${s.label}
                </div>
                ${s.ts ? `<div style="font-size:12px;color:var(--text-muted);white-space:nowrap">${formatDate(s.ts)}</div>` : ''}
              </div>
              <div style="font-size:13px;color:var(--text-muted);margin-top:4px;line-height:1.5">${s.desc}</div>
            </div>
          </div>`;
      }).join('');
    }

    async function loadDetailView(id) {
      document.getElementById('list-view').style.display   = 'none';
      document.getElementById('detail-view').style.display = '';
      showLoading();
      try {
        const res = await api('GET', `/policies/${id}`);
        hideLoading();
        if (!res.success) {
          showToast('Error', res.message || 'Policy not found.', 'error');
          return;
        }
        const p = res.data;

        document.getElementById('detail-policy-num').textContent   = p.policy_number || 'Policy #' + p.id;
        document.getElementById('detail-plan').textContent         = p.plan_name || 'Standard Plan';
        document.getElementById('detail-status-badge').innerHTML   = getStatusBadge(p.status);
        document.getElementById('detail-coverage').textContent     = formatCurrency(p.coverage_amount || 0);
        document.getElementById('detail-crop').textContent         = p.crop_type || '—';
        document.getElementById('detail-location').textContent     = p.farm_location || p.location || '—';
        document.getElementById('detail-filed').textContent        = formatDate(p.created_at);

        const remarksEl = document.getElementById('detail-remarks');
        if (p.remarks || p.rejection_reason) {
          remarksEl.style.display  = '';
          remarksEl.textContent    = '💬 ' + (p.rejection_reason || p.remarks);
          if (p.rejection_reason) remarksEl.style.background = '#fce4ec';
        }

        document.getElementById('timeline-stages').innerHTML = renderTimeline(p);
      } catch (err) {
        hideLoading();
        console.error(err);
        showToast('Error', 'Could not load policy details.', 'error');
      }
    }

    // ── LIST VIEW ────────────────────────────────────────────────

    function renderStatusCard(p) {
      const s          = (p.status || '').toLowerCase();
      const isRejected = s === 'rejected' || s === 'cancelled';
      const step       = isRejected ? -1 : s === 'active' ? 3 : s === 'under_review' ? 2 : 1;
      const pct        = isRejected ? 100 : Math.round((step / 3) * 100);
      const barColor   = isRejected ? 'var(--danger)' : 'var(--primary)';
      const icon       = CROP_ICON[p.crop_type] || '🌱';

      const col = (active) => active ? 'var(--primary)' : 'var(--text-muted)';
      const wt  = (active) => active ? '700' : '400';

      return `
        <div class="card" style="border-radius:14px;overflow:hidden;display:flex;flex-direction:column;transition:box-shadow 0.2s"
          onmouseover="this.style.boxShadow='0 8px 24px rgba(0,0,0,0.12)'"
          onmouseout="this.style.boxShadow=''">
          <div style="background:linear-gradient(135deg,var(--primary-dark),var(--primary));
            padding:16px 20px;display:flex;justify-content:space-between;align-items:center">
            <div style="display:flex;align-items:center;gap:10px">
              <div style="background:rgba(255,255,255,0.15);border-radius:10px;width:40px;height:40px;
                display:flex;align-items:center;justify-content:center;font-size:20px">${icon}</div>
              <div>
                <div style="color:white;font-weight:700;font-size:15px">${p.policy_number || 'Policy #' + p.id}</div>
                <div style="color:rgba(255,255,255,0.75);font-size:12px">${p.plan_name || 'Standard Plan'}</div>
              </div>
            </div>
            <div>${getStatusBadge(p.status)}</div>
          </div>

          <div class="card-body" style="padding:18px 20px;flex:1">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px">
              <div style="background:#f8fafc;border-radius:8px;padding:10px 12px">
                <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:3px">Coverage</div>
                <div style="font-weight:700;color:var(--primary);font-size:15px">${formatCurrency(p.coverage_amount || 0)}</div>
              </div>
              <div style="background:#f8fafc;border-radius:8px;padding:10px 12px">
                <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:3px">Crop Type</div>
                <div style="font-weight:600;font-size:14px">${p.crop_type || '—'}</div>
              </div>
              <div style="background:#f8fafc;border-radius:8px;padding:10px 12px">
                <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:3px">Location</div>
                <div style="font-weight:600;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${p.farm_location || p.location || '—'}</div>
              </div>
              <div style="background:#f8fafc;border-radius:8px;padding:10px 12px">
                <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:3px">Applied</div>
                <div style="font-weight:600;font-size:13px">${formatDate(p.created_at)}</div>
              </div>
            </div>

            <div style="margin-bottom:14px">
              <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--text-muted);margin-bottom:6px">
                <span style="color:${col(step >= 1 && !isRejected)};font-weight:${wt(step >= 1 && !isRejected)}">📋 Submitted</span>
                <span style="color:${col(step >= 2 && !isRejected)};font-weight:${wt(step >= 2 && !isRejected)}">🔍 Under Review</span>
                <span style="color:${isRejected ? 'var(--danger)' : col(step >= 3)};font-weight:${wt(step >= 3 || isRejected)}">
                  ${isRejected ? '❌ Rejected' : '✅ Approved'}
                </span>
              </div>
              <div style="background:#e2e8f0;border-radius:99px;height:6px;overflow:hidden">
                <div style="width:${pct}%;height:100%;background:${barColor};border-radius:99px;transition:width 0.5s ease"></div>
              </div>
              ${p.remarks ? `<p style="margin-top:6px;font-size:12px;color:var(--text-muted);font-style:italic">💬 ${p.remarks}</p>` : ''}
            </div>

            <button class="btn btn-sm btn-outline" style="width:100%"
              onclick="navigateTo('application-status.php?id=${p.id}')">
              🔎 View Full Timeline
            </button>
          </div>
        </div>`;
    }

    function filterStatus(status) {
      activeFilter = status;
      ['all', 'pending', 'under_review', 'active', 'rejected'].forEach(function(key) {
        const btn = document.getElementById('tab-' + key);
        if (!btn) return;
        btn.className = ((key === 'all' && status === '') || key === status)
          ? 'btn btn-primary' : 'btn btn-ghost';
      });
      const filtered = status
        ? allPolicies.filter(p => (p.status || '').toLowerCase() === status)
        : allPolicies;
      renderCards(filtered);
    }

    function renderCards(policies) {
      const container = document.getElementById('status-cards');
      const empty     = document.getElementById('empty-status');
      if (!container) return;
      if (policies.length === 0) {
        container.innerHTML = '';
        if (empty) empty.classList.remove('hidden');
      } else {
        container.innerHTML = policies.map(renderStatusCard).join('');
        if (empty) empty.classList.add('hidden');
      }
    }

    async function loadStatuses() {
      showLoading();
      try {
        const res = await api('GET', '/policies');
        hideLoading();
        if (!res.success) {
          showToast('Error', res.message || 'Failed to load application statuses.', 'error');
          return;
        }
        allPolicies = res.data || [];
        filterStatus('');
      } catch (err) {
        hideLoading();
        console.error(err);
        showToast('Error', 'Could not load application statuses.', 'error');
      }
    }

    // ── Bootstrap ────────────────────────────────────────────────
    const urlId = new URLSearchParams(window.location.search).get('id');
    if (urlId) {
      loadDetailView(urlId);
    } else {
      loadStatuses();
    }
  </script>
</body>
</html>
