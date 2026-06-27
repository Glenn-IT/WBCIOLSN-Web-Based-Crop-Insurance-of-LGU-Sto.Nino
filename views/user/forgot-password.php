<?php
$pageTitle = 'Forgot Password — LGU Sto. Niño Crop Insurance';
$basePath  = '../../';
require_once '../../includes/head.php';
?>
<body>
  <div class="auth-page">
    <div class="auth-container" style="max-width:500px">
      <div class="auth-right" style="flex:1;padding:50px 45px">
        <div style="text-align:center;margin-bottom:28px">
          <div style="font-size:52px;margin-bottom:12px">🔑</div>
          <h2 id="page-title" style="margin-bottom:6px">Forgot Password?</h2>
          <p id="page-subtitle" class="auth-subtitle">Enter your email to reset your password</p>
        </div>

        <!-- Step 1: Request reset link -->
        <div id="step1">
          <div class="form-group">
            <label class="form-label">Email Address</label>
            <div class="input-group">
              <span class="input-icon">📧</span>
              <input type="email" id="reset-email" class="form-control"
                placeholder="your@email.com" />
            </div>
          </div>
          <button class="btn-primary-auth" onclick="sendReset()">
            Send Reset Link →
          </button>
        </div>

        <!-- Step 2: Email sent confirmation -->
        <div id="step2" style="display:none;text-align:center">
          <div style="font-size:48px;margin-bottom:16px">📬</div>
          <h3 style="margin-bottom:8px;color:var(--primary)">Email Sent!</h3>
          <p style="font-size:13.5px;color:var(--text-muted);margin-bottom:24px">
            We've sent a password reset link to your email address. Please check your inbox.
          </p>
          <button class="btn-primary-auth" onclick="logout()">
            Back to Login
          </button>
        </div>

        <!-- Step 3: Set new password (token from URL ?token=...) -->
        <div id="step3" style="display:none">
          <input type="hidden" id="reset-token" />
          <div class="form-group">
            <label class="form-label">New Password</label>
            <div class="input-group">
              <span class="input-icon">🔒</span>
              <input type="password" id="new-password" class="form-control"
                placeholder="Minimum 8 characters"
                oninput="checkStrength(this.value)" />
              <span class="input-icon-right"
                onclick="togglePassword('new-password', this)">👁️</span>
            </div>
            <div id="pw-strength" style="margin-top:6px;font-size:12px;display:none">
              <div style="display:flex;gap:4px;margin-bottom:4px">
                <div id="pw-bar-1" style="height:4px;flex:1;border-radius:2px;background:#ddd"></div>
                <div id="pw-bar-2" style="height:4px;flex:1;border-radius:2px;background:#ddd"></div>
                <div id="pw-bar-3" style="height:4px;flex:1;border-radius:2px;background:#ddd"></div>
                <div id="pw-bar-4" style="height:4px;flex:1;border-radius:2px;background:#ddd"></div>
              </div>
              <div style="color:var(--text-muted)">
                <span id="rule-len"  style="margin-right:8px">❌ 8+ characters</span>
                <span id="rule-up"   style="margin-right:8px">❌ Uppercase</span>
                <span id="rule-num"  style="margin-right:8px">❌ Number</span>
                <span id="rule-spec">❌ Special (!@#$)</span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Confirm New Password</label>
            <div class="input-group">
              <span class="input-icon">🔒</span>
              <input type="password" id="confirm-password" class="form-control"
                placeholder="Re-enter new password" />
            </div>
          </div>
          <button class="btn-primary-auth" onclick="doReset()">
            Set New Password →
          </button>
        </div>

        <!-- Step 4: Reset success -->
        <div id="step4" style="display:none;text-align:center">
          <div style="font-size:48px;margin-bottom:16px">✅</div>
          <h3 style="margin-bottom:8px;color:var(--primary)">Password Reset!</h3>
          <p style="font-size:13.5px;color:var(--text-muted);margin-bottom:24px">
            Your password has been updated. You can now sign in with your new password.
          </p>
          <button class="btn-primary-auth" onclick="logout()">
            Go to Login →
          </button>
        </div>

        <div style="text-align:center;margin-top:20px">
          <a class="auth-link" onclick="logout()">← Back to Login</a>
        </div>
      </div>
    </div>
  </div>

  <?php require_once '../../includes/toast.php'; ?>
  <script>
    // On load: check for ?token= in URL to show reset form directly
    (function () {
      const params = new URLSearchParams(window.location.search);
      const token  = params.get('token');
      if (token) {
        document.getElementById('reset-token').value = token;
        document.getElementById('step1').style.display = 'none';
        document.getElementById('step3').style.display = 'block';
        document.getElementById('page-title').textContent    = 'Set New Password';
        document.getElementById('page-subtitle').textContent = 'Enter and confirm your new password below.';
      }
    })();

    async function sendReset() {
      const email = document.getElementById('reset-email').value.trim();
      if (!email) { showToast('Error', 'Please enter your email.', 'error'); return; }
      showLoading();
      try {
        await api('POST', '/auth/forgot-password', { email }, false);
        hideLoading();
        showToast('Email Sent', 'If that email is registered, a reset link has been sent.', 'success');
        document.getElementById('step1').style.display = 'none';
        document.getElementById('step2').style.display = 'block';
      } catch (err) {
        hideLoading();
        showToast('Error', 'Could not reach the server.', 'error');
      }
    }

    async function doReset() {
      const token   = document.getElementById('reset-token').value;
      const newPass = document.getElementById('new-password').value;
      const confirm = document.getElementById('confirm-password').value;
      if (!newPass || !confirm) { showToast('Error', 'Please fill in both password fields.', 'error'); return; }
      if (newPass !== confirm)  { showToast('Error', 'Passwords do not match.', 'error'); return; }
      if (newPass.length < 8)   { showToast('Error', 'Password must be at least 8 characters.', 'error'); return; }
      showLoading();
      try {
        const res = await api('POST', '/auth/reset-password', { token, password: newPass }, false);
        hideLoading();
        if (res.success) {
          document.getElementById('step3').style.display = 'none';
          document.getElementById('step4').style.display = 'block';
          document.getElementById('page-title').textContent    = 'Success!';
          document.getElementById('page-subtitle').textContent = '';
        } else {
          showToast('Error', res.message || 'Invalid or expired reset link.', 'error');
        }
      } catch (err) {
        hideLoading();
        showToast('Error', 'Could not reach the server.', 'error');
      }
    }

    function checkStrength(v) {
      const box = document.getElementById('pw-strength');
      box.style.display = v ? 'block' : 'none';
      if (!v) return;
      const rules = [
        { id: 'rule-len',  ok: v.length >= 8,   label: '8+ characters' },
        { id: 'rule-up',   ok: /[A-Z]/.test(v), label: 'Uppercase' },
        { id: 'rule-num',  ok: /[0-9]/.test(v), label: 'Number' },
        { id: 'rule-spec', ok: /[\W_]/.test(v), label: 'Special (!@#$)' },
      ];
      let score = 0;
      rules.forEach(r => {
        const el = document.getElementById(r.id);
        el.textContent = (r.ok ? '✅' : '❌') + ' ' + r.label;
        el.style.color = r.ok ? '#28a745' : '#dc3545';
        if (r.ok) score++;
      });
      const colors = ['#ddd', '#dc3545', '#fd7e14', '#ffc107', '#28a745'];
      for (let i = 1; i <= 4; i++) {
        document.getElementById('pw-bar-' + i).style.background = i <= score ? colors[score] : '#ddd';
      }
    }
  </script>
</body>
</html>
