<?php
$pageTitle   = 'SMS Logs — Admin';
$basePath    = '../../';
$currentPage = 'sms-logs';
$guardRole   = 'admin';
require_once '../../includes/auth-guard.php';
require_once '../../includes/head.php';
?>
<body>
  <div class="app-layout">

    <?php require_once '../../includes/admin-sidebar.php'; ?>

    <?php
    $topbarTitle    = 'SMS Delivery Logs';
    $topbarSubtitle = 'Monitor, audit, and resend PhilSMS Gateway transmissions';
    $isAdmin        = true;
    require_once '../../includes/topbar.php';
    ?>

    <main class="main-content">
      <div class="page-header">
        <div class="page-header-left">
          <div class="breadcrumb">
            <span>Admin</span><span class="sep">›</span>
            <span class="current">SMS Logs</span>
          </div>
          <h2>📱 SMS Delivery Logs</h2>
          <p>Real-time audit log of all outgoing SMS notifications and delivery statuses</p>
        </div>
        <div class="page-header-right" style="display:flex;gap:10px">
          <a href="sms-test.php" class="btn btn-outline">🧪 Test Gateway</a>
          <button class="btn btn-primary" onclick="loadSmsLogs()">🔄 Refresh Logs</button>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px">
        <div class="stat-card" style="--stat-color:#1a237e">
          <div class="stat-icon" style="background:#e8eaf6;color:#1a237e">📱</div>
          <div class="stat-info"><h3 id="stat-total">0</h3><p>Total Attempts</p></div>
        </div>
        <div class="stat-card" style="--stat-color:var(--success)">
          <div class="stat-icon" style="background:#d4edda;color:var(--success)">✅</div>
          <div class="stat-info"><h3 id="stat-sent">0</h3><p>Sent Successfully</p></div>
        </div>
        <div class="stat-card" style="--stat-color:var(--danger)">
          <div class="stat-icon" style="background:#f8d7da;color:var(--danger)">❌</div>
          <div class="stat-info"><h3 id="stat-failed">0</h3><p>Failed Attempts</p></div>
        </div>
        <div class="stat-card" style="--stat-color:var(--warning)">
          <div class="stat-icon" style="background:#fff3cd;color:#856404">⚠️</div>
          <div class="stat-info"><h3 id="stat-simulated">0</h3><p>Simulated / Unconfigured</p></div>
        </div>
      </div>

      <!-- Main Card -->
      <div class="card">
        <div class="card-header">
          <h5>📱 Outgoing Messages</h5>
          <div class="search-filter-bar" style="flex:1;margin-left:20px">
            <div class="search-input-wrapper">
              <span class="search-icon">🔍</span>
              <input type="text" class="form-control" id="search-input"
                placeholder="Search by phone number or message content..." oninput="filterLogs()" />
            </div>
            <select class="form-select" id="status-filter" onchange="filterLogs()" style="width:160px">
              <option value="">All Status</option>
              <option value="sent">✅ Sent</option>
              <option value="failed">❌ Failed</option>
              <option value="simulated">⚠️ Simulated</option>
            </select>
            <button class="btn btn-sm btn-ghost" onclick="clearLogsConfirm()" title="Clear all logs" style="color:var(--danger)">🗑️ Clear Logs</button>
          </div>
        </div>
        <div class="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Recipient</th>
                <th>Message Content</th>
                <th>Status</th>
                <th>HTTP Code</th>
                <th>Date &amp; Time</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="sms-table-body">
              <tr>
                <td colspan="7" style="text-align:center;color:var(--text-muted);padding:30px">Loading SMS logs...</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div id="empty-state" class="empty-state hidden">
          <div class="empty-icon">📱</div>
          <h4>No SMS Logs Found</h4>
          <p>No messages match your search or filter settings.</p>
        </div>
        <div id="pagination-bar"
          style="display:flex;align-items:center;justify-content:space-between;
            padding:14px 20px;border-top:1px solid var(--border-color);
            font-size:13px;color:var(--text-muted)">
          <span id="pagination-info">Showing 0 results</span>
          <div style="display:flex;gap:6px" id="pagination-btns"></div>
        </div>
      </div>
    </main>
  </div>

  <!-- Detail Modal -->
  <div class="modal-overlay" id="sms-modal">
    <div class="modal modal-lg">
      <div class="modal-header">
        <h4>📱 SMS Log Details — <span id="modal-sms-id"></span></h4>
        <button class="modal-close" onclick="closeModal('sms-modal')">×</button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="detail-log-id" />

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px">
          <div>
            <label class="form-label" style="color:var(--text-muted);font-size:12.5px">Recipient Number</label>
            <div id="modal-recipient" style="font-size:16px;font-weight:700;color:var(--text-dark)"></div>
          </div>
          <div>
            <label class="form-label" style="color:var(--text-muted);font-size:12.5px">Delivery Status</label>
            <div id="modal-status-badge"></div>
          </div>
          <div>
            <label class="form-label" style="color:var(--text-muted);font-size:12.5px">HTTP Response Code</label>
            <div id="modal-http-code" style="font-size:14px;font-weight:600"></div>
          </div>
          <div>
            <label class="form-label" style="color:var(--text-muted);font-size:12.5px">Sent Timestamp</label>
            <div id="modal-created-at" style="font-size:14px"></div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Message Body</label>
          <div id="modal-message-box" style="background:#f8fafc;border:1px solid var(--border-color);
            border-radius:8px;padding:12px 14px;font-size:13.5px;line-height:1.5;white-space:pre-wrap"></div>
        </div>

        <div id="modal-error-group" class="form-group" style="display:none">
          <label class="form-label" style="color:var(--danger)">Error Message</label>
          <div id="modal-error-box" style="background:#fff0f0;border:1px solid #f5c6cb;color:#721c24;
            border-radius:8px;padding:10px 14px;font-size:13px"></div>
        </div>

        <div class="form-group">
          <label class="form-label">Gateway Raw Response (PhilSMS)</label>
          <pre id="modal-response-box" style="background:#1e1e1e;color:#38bdf8;padding:14px;
            border-radius:8px;font-size:12px;max-height:180px;overflow:auto;margin:0"></pre>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-ghost" onclick="closeModal('sms-modal')">Close</button>
        <button class="btn btn-primary" onclick="resendCurrentSms()">🔄 Resend SMS</button>
      </div>
    </div>
  </div>

  <!-- Clear Confirmation Modal -->
  <div class="modal-overlay" id="clear-confirm-modal">
    <div class="modal" style="max-width:420px;text-align:center">
      <div class="modal-body" style="padding:32px 28px">
        <div style="font-size:48px;margin-bottom:12px">🗑️</div>
        <h4 style="margin-bottom:8px;color:var(--danger)">Clear All SMS Logs?</h4>
        <p style="color:var(--text-muted);font-size:13.5px;margin-bottom:24px">
          This will permanently delete all SMS delivery records from the system log.
        </p>
        <div style="display:flex;gap:12px;justify-content:center">
          <button class="btn btn-ghost" onclick="closeModal('clear-confirm-modal')">Cancel</button>
          <button class="btn btn-danger" onclick="executeClearLogs()">🗑️ Yes, Clear All</button>
        </div>
      </div>
    </div>
  </div>

  <?php require_once '../../includes/toast.php'; ?>
  <script>
    initTopbarUser();

    let allLogs      = [];
    let filteredLogs = [];
    let currentPage  = 1;
    const PER_PAGE   = 10;
    let selectedLog  = null;

    function getSmsBadge(status) {
      const s = (status || '').toLowerCase();
      if (s === 'sent')      return '<span class="badge badge-success">✅ Sent</span>';
      if (s === 'failed')    return '<span class="badge badge-danger">❌ Failed</span>';
      if (s === 'simulated') return '<span class="badge badge-warning">⚠️ Simulated</span>';
      return `<span class="badge badge-secondary">${status}</span>`;
    }

    async function loadSmsLogs() {
      showLoading();
      try {
        const res = await api('GET', '/sms-logs?per_page=200');
        hideLoading();
        if (!res.success) { showToast('Error', 'Failed to load SMS logs.', 'error'); return; }
        allLogs = res.data || [];
        filteredLogs = [...allLogs];
        renderStats(res.stats || {});
        filterLogs();
      } catch (err) {
        hideLoading();
        console.error(err);
        showToast('Error', 'Error connecting to server.', 'error');
      }
    }

    function renderStats(stats) {
      const set = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
      set('stat-total',     stats.total     || allLogs.length);
      set('stat-sent',      stats.sent      || allLogs.filter(l => l.status === 'sent').length);
      set('stat-failed',    stats.failed    || allLogs.filter(l => l.status === 'failed').length);
      set('stat-simulated', stats.simulated || allLogs.filter(l => l.status === 'simulated').length);
    }

    function filterLogs() {
      const q      = (document.getElementById('search-input')?.value || '').toLowerCase();
      const status = document.getElementById('status-filter')?.value || '';
      filteredLogs = allLogs.filter(l => {
        const matchText   = !q || (l.recipient || '').toLowerCase().includes(q) || (l.message || '').toLowerCase().includes(q);
        const matchStatus = !status || l.status === status;
        return matchText && matchStatus;
      });
      currentPage = 1;
      renderTable();
      renderPagination();
    }

    function getLogById(id) {
      return allLogs.find(l => l.id == id) || null;
    }

    function renderTable() {
      const tbody = document.getElementById('sms-table-body');
      const empty = document.getElementById('empty-state');
      if (!tbody) return;

      const start    = (currentPage - 1) * PER_PAGE;
      const pageData = filteredLogs.slice(start, start + PER_PAGE);

      if (!filteredLogs.length) {
        tbody.innerHTML = '';
        if (empty) empty.classList.remove('hidden');
        return;
      }
      if (empty) empty.classList.add('hidden');

      tbody.innerHTML = pageData.map(l => {
        const snippet = l.message.length > 55 ? l.message.substring(0, 55) + '...' : l.message;
        const codeDisplay = l.http_code ? `<span style="font-weight:600">${l.http_code}</span>` : '—';
        return `
          <tr>
            <td><strong>#${l.id}</strong></td>
            <td><strong style="color:var(--primary)">${l.recipient}</strong></td>
            <td><span style="font-size:13px" title="${escapeHtml(l.message)}">${escapeHtml(snippet)}</span></td>
            <td>${getSmsBadge(l.status)}</td>
            <td>${codeDisplay}</td>
            <td style="white-space:nowrap">${formatDate(l.created_at)}</td>
            <td style="white-space:nowrap">
              <button class="btn btn-sm btn-outline" title="View Log Details" onclick="viewSmsModal(${l.id})">👁️ Details</button>
              <button class="btn btn-sm btn-primary" title="Resend Message" onclick="resendSmsById(${l.id})">🔄 Resend</button>
            </td>
          </tr>`;
      }).join('');
    }

    function escapeHtml(text) {
      if (!text) return '';
      return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    function renderPagination() {
      const total      = filteredLogs.length;
      const totalPages = Math.ceil(total / PER_PAGE);
      const start      = total ? (currentPage - 1) * PER_PAGE + 1 : 0;
      const end        = Math.min(currentPage * PER_PAGE, total);
      const info       = document.getElementById('pagination-info');
      if (info) info.textContent = `Showing ${start}–${end} of ${total} SMS record${total !== 1 ? 's' : ''}`;
      const btns = document.getElementById('pagination-btns');
      if (!btns) return;
      let html = `<button class="btn btn-sm btn-ghost" ${currentPage <= 1 ? 'disabled' : ''} onclick="goPage(${currentPage - 1})">‹ Prev</button>`;
      for (let p = 1; p <= totalPages; p++) {
        html += `<button class="btn btn-sm ${p === currentPage ? 'btn-primary' : 'btn-ghost'}" onclick="goPage(${p})">${p}</button>`;
      }
      html += `<button class="btn btn-sm btn-ghost" ${currentPage >= totalPages ? 'disabled' : ''} onclick="goPage(${currentPage + 1})">Next ›</button>`;
      btns.innerHTML = html;
    }

    function goPage(p) {
      const totalPages = Math.ceil(filteredLogs.length / PER_PAGE);
      if (p < 1 || p > totalPages) return;
      currentPage = p;
      renderTable();
      renderPagination();
    }

    function viewSmsModal(id) {
      selectedLog = getLogById(id);
      if (!selectedLog) return;
      const l = selectedLog;

      document.getElementById('detail-log-id').value       = l.id;
      document.getElementById('modal-sms-id').textContent    = '#' + l.id;
      document.getElementById('modal-recipient').textContent = l.recipient;
      document.getElementById('modal-status-badge').innerHTML = getSmsBadge(l.status);
      document.getElementById('modal-http-code').textContent  = l.http_code ? 'HTTP ' + l.http_code : 'N/A';
      document.getElementById('modal-created-at').textContent = formatDate(l.created_at);
      document.getElementById('modal-message-box').textContent = l.message;

      const errGroup = document.getElementById('modal-error-group');
      const errBox   = document.getElementById('modal-error-box');
      if (l.error_message) {
        errBox.textContent = l.error_message;
        errGroup.style.display = 'block';
      } else {
        errGroup.style.display = 'none';
      }

      let respPretty = 'No raw response stored.';
      if (l.response_body) {
        try {
          const parsed = JSON.parse(l.response_body);
          respPretty = JSON.stringify(parsed, null, 2);
        } catch (e) {
          respPretty = l.response_body;
        }
      }
      document.getElementById('modal-response-box').textContent = respPretty;

      openModal('sms-modal');
    }

    async function resendSmsById(id) {
      showLoading();
      try {
        const res = await api('POST', `/sms-logs/${id}/resend`);
        hideLoading();
        if (res.success) {
          showToast('Completed', 'SMS resend action finished.', 'success');
          await loadSmsLogs();
        } else {
          showToast('Error', res.message || 'Failed to resend SMS.', 'error');
        }
      } catch (err) {
        hideLoading();
        console.error(err);
        showToast('Error', 'An error occurred during resend.', 'error');
      }
    }

    function resendCurrentSms() {
      const id = document.getElementById('detail-log-id')?.value;
      if (!id) return;
      closeModal('sms-modal');
      resendSmsById(id);
    }

    function clearLogsConfirm() {
      openModal('clear-confirm-modal');
    }

    async function executeClearLogs() {
      closeModal('clear-confirm-modal');
      showLoading();
      try {
        const res = await api('DELETE', '/sms-logs/clear');
        hideLoading();
        if (res.success) {
          showToast('Cleared', 'All SMS logs have been cleared.', 'success');
          await loadSmsLogs();
        } else {
          showToast('Error', res.message || 'Failed to clear logs.', 'error');
        }
      } catch (err) {
        hideLoading();
        console.error(err);
        showToast('Error', 'Error clearing logs.', 'error');
      }
    }

    loadSmsLogs();
  </script>
</body>
</html>
