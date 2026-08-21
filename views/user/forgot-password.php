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
          <div id="page-icon" style="font-size:52px;margin-bottom:12px">🔑</div>
          <h2 id="page-title" style="margin-bottom:6px">Forgot Password?</h2>
          <p id="page-subtitle" class="auth-subtitle">Enter your registered email address to receive a verification code.</p>
        </div>

        <!-- Step 1: Enter Email -->
        <form id="step1" onsubmit="event.preventDefault(); requestOtp();">
          <div class="form-group">
            <label class="form-label">Email Address</label>
            <div class="input-group">
              <span class="input-icon">📧</span>
              <input type="email" id="reset-email" class="form-control"
                placeholder="your@email.com" required autofocus />
            </div>
          </div>
          <button type="submit" id="btn-send-otp" class="btn-primary-auth">
            Send Verification Code →
          </button>
        </form>

        <!-- Step 2: Verify OTP Code -->
        <form id="step2" style="display:none" onsubmit="event.preventDefault(); verifyOtpCode();">
          <div class="form-group" style="text-align:center;margin-bottom:20px;">
            <label class="form-label" style="text-align:left;display:block">6-Digit Verification Code</label>
            <div class="input-group">
              <span class="input-icon">🔢</span>
              <input type="text" id="reset-otp" class="form-control"
                placeholder="000000" maxlength="6" inputmode="numeric" pattern="[0-9]*"
                style="font-size:22px;letter-spacing:6px;font-weight:700;text-align:center;"
                oninput="this.value = this.value.replace(/[^0-9]/g, '')" required />
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px;font-size:12.5px;">
              <span style="color:var(--text-muted)">Didn't receive the code?</span>
              <button type="button" id="btn-resend-otp" onclick="resendOtp()" 
                style="background:none;border:none;color:var(--primary,#2e7d32);font-weight:600;cursor:pointer;padding:0;font-size:12.5px;">
                Resend Code
              </button>
            </div>
          </div>

          <button type="submit" id="btn-verify-otp" class="btn-primary-auth">
            Verify Code →
          </button>

          <div style="text-align:center;margin-top:14px">
            <a class="auth-link" href="javascript:void(0)" onclick="goToStep1()" style="font-size:13px">
              ← Use a different email
            </a>
          </div>
        </form>

        <!-- Step 3: Set New Password (Shown ONLY after OTP is verified) -->
        <form id="step3" style="display:none" onsubmit="event.preventDefault(); submitNewPassword();">
          
          <!-- Password Requirements Info -->
          <div style="
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-left: 4px solid #2196F3;
            padding: 10px 12px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 11px;
          ">
            <div style="display: flex; align-items: start; gap: 8px;">
              <span style="font-size: 16px;">ℹ️</span>
              <div style="flex: 1; color: #424242; line-height: 1.5;">
                <strong style="color: #1976d2; display: block; margin-bottom: 3px;">Password must contain:</strong>
                8+ chars • Uppercase • Lowercase • Number • Special (@#!)
                <br><strong style="color: #2e7d32;">Example:</strong> 
                <code style="background: rgba(46,125,50,0.1); padding: 1px 4px; border-radius: 2px; font-weight: 600; color: #2e7d32;">Password@123</code>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <label class="form-label">New Password</label>
            <div class="input-group">
              <span class="input-icon">🔒</span>
              <input type="password" id="new-password" class="form-control"
                placeholder="Minimum 8 characters" required
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
                placeholder="Re-enter new password" required />
              <span class="input-icon-right"
                onclick="togglePassword('confirm-password', this)">👁️</span>
            </div>
          </div>

          <button type="submit" id="btn-save-password" class="btn-primary-auth">
            Set New Password →
          </button>
        </form>

        <!-- Step 4: Reset success -->
        <div id="step4" style="display:none;text-align:center">
          <div style="font-size:48px;margin-bottom:16px">✅</div>
          <h3 style="margin-bottom:8px;color:var(--primary)">Password Reset Complete!</h3>
          <p style="font-size:13.5px;color:var(--text-muted);margin-bottom:24px">
            Your password has been successfully updated. You can now sign in with your new password.
          </p>
          <button class="btn-primary-auth" onclick="window.location.replace('../../index.php')">
            Go to Login →
          </button>
        </div>

        <div id="back-to-login-container" style="text-align:center;margin-top:20px">
          <a class="auth-link" href="../../index.php">← Back to Login</a>
        </div>
      </div>
    </div>
  </div>

  <?php require_once '../../includes/toast.php'; ?>
  <script>
    let resetEmail = '';
    let resetToken = '';
    let resendTimer = null;
    let resendCountdown = 0;

    // STEP 1: Request OTP
    async function requestOtp() {
      const email = document.getElementById('reset-email').value.trim();
      if (!email) {
        showToast('Error', 'Please enter your email address.', 'error');
        return;
      }

      const btn = document.getElementById('btn-send-otp');
      btn.disabled = true;
      showLoading();

      try {
        const res = await api('POST', '/auth/forgot-password', { email }, false);
        hideLoading();
        btn.disabled = false;

        if (res.success) {
          resetEmail = email;
          document.getElementById('step1').style.display = 'none';
          document.getElementById('step2').style.display = 'block';
          document.getElementById('step3').style.display = 'none';
          document.getElementById('step4').style.display = 'none';
          document.getElementById('back-to-login-container').style.display = 'none';
          
          document.getElementById('page-icon').textContent     = '✉️';
          document.getElementById('page-title').textContent    = 'Enter Verification Code';
          document.getElementById('page-subtitle').innerHTML   = `We sent a 6-digit code to <strong>${escapeHtml(email)}</strong>.`;

          showToast('Code Sent', res.message || 'Verification code sent to your email.', 'success');
          
          // Auto-fill debug OTP in development if returned
          if (res.data && res.data.debug_otp) {
            document.getElementById('reset-otp').value = res.data.debug_otp;
          }

          startResendCountdown(60);
          document.getElementById('reset-otp').focus();
        } else {
          showToast('Unable to Proceed', res.message || 'No account found with that email.', 'error');
        }
      } catch (err) {
        hideLoading();
        btn.disabled = false;
        showToast('Error', 'Could not reach the server. Please check your connection.', 'error');
      }
    }

    // STEP 2: Verify OTP
    async function verifyOtpCode() {
      const otp = document.getElementById('reset-otp').value.trim();
      if (!otp || otp.length !== 6) {
        showToast('Invalid Code', 'Please enter the 6-digit verification code.', 'error');
        document.getElementById('reset-otp').focus();
        return;
      }

      const btn = document.getElementById('btn-verify-otp');
      btn.disabled = true;
      showLoading();

      try {
        const res = await api('POST', '/auth/verify-otp', {
          email: resetEmail,
          otp
        }, false);

        hideLoading();
        btn.disabled = false;

        if (res.success) {
          resetToken = res.data?.reset_token || '';
          clearInterval(resendTimer);

          // Transition to Step 3 (Set New Password)
          document.getElementById('step2').style.display = 'none';
          document.getElementById('step3').style.display = 'block';
          
          document.getElementById('page-icon').textContent     = '🔒';
          document.getElementById('page-title').textContent    = 'Set New Password';
          document.getElementById('page-subtitle').textContent = 'Enter and confirm your new password below.';

          showToast('Code Verified', 'Please enter your new password.', 'success');
          document.getElementById('new-password').focus();
        } else {
          showToast('Verification Failed', res.message || 'Invalid or expired verification code.', 'error');
        }
      } catch (err) {
        hideLoading();
        btn.disabled = false;
        showToast('Error', 'Could not reach the server.', 'error');
      }
    }

    // Resend OTP code
    async function resendOtp() {
      if (resendCountdown > 0) return;
      if (!resetEmail) {
        goToStep1();
        return;
      }

      showLoading();
      try {
        const res = await api('POST', '/auth/forgot-password', { email: resetEmail }, false);
        hideLoading();

        if (res.success) {
          showToast('Code Resent', 'A new verification code has been sent.', 'success');
          if (res.data && res.data.debug_otp) {
            document.getElementById('reset-otp').value = res.data.debug_otp;
          }
          startResendCountdown(60);
        } else {
          showToast('Error', res.message || 'Could not resend verification code.', 'error');
        }
      } catch (err) {
        hideLoading();
        showToast('Error', 'Could not reach the server.', 'error');
      }
    }

    function startResendCountdown(seconds) {
      clearInterval(resendTimer);
      resendCountdown = seconds;
      const resendBtn = document.getElementById('btn-resend-otp');
      resendBtn.disabled = true;
      resendBtn.style.opacity = '0.6';
      resendBtn.style.cursor = 'default';
      resendBtn.textContent = `Resend code in ${resendCountdown}s`;

      resendTimer = setInterval(() => {
        resendCountdown--;
        if (resendCountdown <= 0) {
          clearInterval(resendTimer);
          resendBtn.disabled = false;
          resendBtn.style.opacity = '1';
          resendBtn.style.cursor = 'pointer';
          resendBtn.textContent = 'Resend Code';
        } else {
          resendBtn.textContent = `Resend code in ${resendCountdown}s`;
        }
      }, 1000);
    }

    function goToStep1() {
      clearInterval(resendTimer);
      document.getElementById('step2').style.display = 'none';
      document.getElementById('step3').style.display = 'none';
      document.getElementById('step4').style.display = 'none';
      document.getElementById('step1').style.display = 'block';
      document.getElementById('back-to-login-container').style.display = 'block';

      document.getElementById('page-icon').style.display   = 'block';
      document.getElementById('page-icon').textContent     = '🔑';
      document.getElementById('page-title').textContent    = 'Forgot Password?';
      document.getElementById('page-subtitle').textContent = 'Enter your registered email address to receive a verification code.';
      document.getElementById('reset-otp').value = '';
    }

    // STEP 3: Submit New Password
    async function submitNewPassword() {
      const newPass = document.getElementById('new-password').value;
      const confirm = document.getElementById('confirm-password').value;

      if (!newPass || !confirm) {
        showToast('Error', 'Please fill in both password fields.', 'error');
        return;
      }
      if (newPass !== confirm) {
        showToast('Error', 'Passwords do not match.', 'error');
        return;
      }
      if (newPass.length < 8) {
        showToast('Error', 'Password must be at least 8 characters.', 'error');
        return;
      }

      const btn = document.getElementById('btn-save-password');
      btn.disabled = true;
      showLoading();

      try {
        const payload = {
          email: resetEmail,
          password: newPass
        };
        if (resetToken) {
          payload.reset_token = resetToken;
        }

        const res = await api('POST', '/auth/reset-password', payload, false);

        hideLoading();
        btn.disabled = false;

        if (res.success) {
          document.getElementById('step3').style.display = 'none';
          document.getElementById('step4').style.display = 'block';
          document.getElementById('page-icon').style.display   = 'none';
          document.getElementById('page-title').textContent    = '';
          document.getElementById('page-subtitle').textContent = '';
        } else {
          showToast('Reset Failed', res.message || 'Failed to update password. Please try again.', 'error');
        }
      } catch (err) {
        hideLoading();
        btn.disabled = false;
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
        if (el) {
          el.textContent = (r.ok ? '✅' : '❌') + ' ' + r.label;
          el.style.color = r.ok ? '#28a745' : '#dc3545';
        }
        if (r.ok) score++;
      });
      const colors = ['#ddd', '#dc3545', '#fd7e14', '#ffc107', '#28a745'];
      for (let i = 1; i <= 4; i++) {
        const bar = document.getElementById('pw-bar-' + i);
        if (bar) bar.style.background = i <= score ? colors[score] : '#ddd';
      }
    }

    function escapeHtml(str) {
      return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
  </script>
</body>
</html>
