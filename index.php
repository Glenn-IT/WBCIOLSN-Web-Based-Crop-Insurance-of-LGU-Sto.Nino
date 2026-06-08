<?php
$pageTitle = 'Login — LGU Sto. Niño Crop Insurance';
$basePath  = './';
require_once 'includes/head.php';
?>
<body>
  <div class="auth-page">
    <div class="auth-container">

      <!-- Left Panel -->
      <div class="auth-left">
        <div class="auth-logo">🌽</div>
        <h1>LGU Sto. Niño<br />Crop Insurance System</h1>
        <p>
          A comprehensive digital platform for managing crop insurance
          applications, claims, and farmer support programs.
        </p>
        <ul class="auth-features">
          <li><span class="icon">📋</span> Easy Online Application</li>
          <li><span class="icon">📊</span> Real-time Status Tracking</li>
          <li><span class="icon">💰</span> Fast Claims Processing</li>
          <li><span class="icon">🔒</span> Secure &amp; Reliable</li>
          <li><span class="icon">📱</span> Mobile Friendly</li>
        </ul>
      </div>

      <!-- Right Panel -->
      <div class="auth-right">
        <h2>Welcome Back 👋</h2>
        <p class="auth-subtitle">Sign in to your farmer account</p>

        <form id="login-form" onsubmit="handleLogin(event)">
          <div class="form-group">
            <label class="form-label">Email Address</label>
            <div class="input-group">
              <span class="input-icon">📧</span>
              <input
                type="email"
                id="login-email"
                class="form-control"
                placeholder="you@example.com"
                required
              />
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" style="display:flex;justify-content:space-between">
              Password
              <a class="auth-link" style="font-weight:500;font-size:13px"
                onclick="navigateTo('views/user/forgot-password.php')">
                Forgot password?
              </a>
            </label>
            <div class="input-group">
              <span class="input-icon">🔒</span>
              <input
                type="password"
                id="login-password"
                class="form-control"
                placeholder="Enter your password"
                required
              />
              <span class="input-icon-right"
                onclick="togglePassword('login-password', this)">👁️</span>
            </div>
          </div>

          <div class="form-group"
            style="display:flex;align-items:center;gap:8px;margin-bottom:22px">
            <input type="checkbox" id="remember"
              style="accent-color:var(--primary);width:15px;height:15px" />
            <label for="remember"
              style="font-size:13px;color:var(--text-secondary);cursor:pointer">
              Remember me
            </label>
          </div>

          <button type="submit" class="btn-primary-auth">Sign In →</button>
        </form>

        <div class="auth-divider">or</div>

        <div style="text-align:center">
          <p style="font-size:13.5px;color:var(--text-muted)">
            Don't have an account?
            <a class="auth-link" onclick="navigateTo('views/user/signup.php')">
              Register here
            </a>
          </p>
        </div>

        <div style="text-align:center;margin-top:14px">
          <a class="auth-link" style="font-size:13px"
            onclick="navigateTo('views/admin/login.php')">
            🛡️ Admin / Agent Login
          </a>
        </div>
      </div>

    </div>
  </div>

  <?php $basePath = './'; require_once 'includes/toast.php'; ?>
  <script>
    async function handleLogin(e) {
      e.preventDefault();
      const email    = document.getElementById('login-email').value.trim();
      const password = document.getElementById('login-password').value;
      const btn      = e.target.querySelector('button[type=submit]');

      btn.disabled = true;
      showLoading();
      try {
        const res = await api('POST', '/auth/login', { email, password }, false);
        hideLoading();
        if (res.success) {
          setSession(res.data.user, res.data.token);
          const role = res.data.user.role;
          showToast('Welcome back!', `Hello ${res.data.user.first_name}! Redirecting...`, 'success');
          setTimeout(() => {
            if (role === 'admin' || role === 'agent') {
              window.location.href = 'views/admin/dashboard.php';
            } else {
              window.location.href = 'views/user/dashboard.php';
            }
          }, 1000);
        } else {
          showToast('Login Failed', res.message || 'Invalid email or password.', 'error');
          btn.disabled = false;
        }
      } catch (err) {
        hideLoading();
        showToast('Error', 'Could not reach the server. Check your connection.', 'error');
        btn.disabled = false;
      }
    }
  </script>
</body>
</html>
