<?php
$pageTitle = 'Admin Login — LGU Crop Insurance';
$basePath  = '../../';
require_once '../../includes/head.php';
?>
<body>
  <div class="auth-page"
    style="background:linear-gradient(135deg,#1a237e 0%,#283593 40%,#1a6b3c 100%)">
    <div class="auth-container">

      <div class="auth-left" style="background:linear-gradient(160deg,#1a237e,#283593)">
        <div class="auth-logo" style="font-size:36px">🛡️</div>
        <h1>LGU Sto. Niño<br />Admin Panel</h1>
        <p>
          Secure administrative portal for the Agricultural Office.
          Authorized personnel only.
        </p>
        <ul class="auth-features">
          <li><span class="icon">📊</span>Real-time Analytics Dashboard</li>
          <li><span class="icon">⚙️</span>Application Management</li>
          <li><span class="icon">✅</span>Claim Verification</li>
          <li><span class="icon">📈</span>Generate Reports</li>
          <li><span class="icon">🔒</span>Secure Access</li>
        </ul>
      </div>

      <div class="auth-right">
        <div style="text-align:center;margin-bottom:28px">
          <div style="width:60px;height:60px;background:linear-gradient(135deg,#1a237e,#283593);
            border-radius:14px;display:flex;align-items:center;justify-content:center;
            font-size:26px;margin:0 auto 16px">
            🛡️
          </div>
        </div>
        <h2>Administrator Login</h2>
        <p class="auth-subtitle">Sign in with your admin credentials</p>

        <form onsubmit="handleAdminLogin(event)">
          <div class="form-group">
            <label class="form-label">Admin Email</label>
            <div class="input-group">
              <span class="input-icon">📧</span>
              <input type="email" id="admin-email" class="form-control"
                placeholder="admin@lgu-stonino.gov.ph" required />
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Password</label>
            <div class="input-group">
              <span class="input-icon">🔒</span>
              <input type="password" id="admin-password" class="form-control"
                placeholder="Admin password" required />
              <span class="input-icon-right"
                onclick="togglePassword('admin-password', this)">👁️</span>
            </div>
          </div>
          <button type="submit" class="btn-primary-auth"
            style="background:linear-gradient(135deg,#1a237e,#283593)">
            Sign In as Admin →
          </button>
        </form>

        <div style="text-align:center;margin-top:20px">
          <a class="auth-link" style="font-size:13px"
            onclick="navigateTo('../../index.php')">← Back to Farmer Login</a>
        </div>
      </div>

    </div>
  </div>

  <?php require_once '../../includes/toast.php'; ?>
  <script>
    async function handleAdminLogin(e) {
      e.preventDefault();
      const email    = document.getElementById('admin-email').value.trim();
      const password = document.getElementById('admin-password').value;
      const btn      = e.target.querySelector('button[type=submit]');

      btn.disabled = true;
      showLoading();
      try {
        const res = await api('POST', '/auth/login', { email, password }, false);
        hideLoading();
        if (res.success) {
          const role = res.data.user.role;
          if (role !== 'admin' && role !== 'agent') {
            showToast('Access Denied', 'This portal is for admins and agents only.', 'error');
            btn.disabled = false;
            return;
          }
          setSession(res.data.user, res.data.token);
          showToast('Welcome!', 'Redirecting to dashboard...', 'success');
          setTimeout(() => window.location.href = 'dashboard.php', 1000);
        } else {
          showToast('Login Failed', res.message || 'Invalid credentials.', 'error');
          btn.disabled = false;
        }
      } catch (err) {
        hideLoading();
        showToast('Error', 'Could not reach the server.', 'error');
        btn.disabled = false;
      }
    }
  </script>
</body>
</html>
