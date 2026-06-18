<?php
$pageTitle = 'Sign Up — LGU Sto. Niño Crop Insurance';
$basePath  = '../../';
require_once '../../includes/head.php';
?>
<body>
  <div class="auth-page">
    <div class="auth-container" style="max-width:1100px">

      <!-- Left Panel -->
      <div class="auth-left">
        <div class="auth-logo">🌽</div>
        <h1>Join the LGU Sto. Niño Crop Insurance Program</h1>
        <p>
          Register as a farmer to access insurance coverage, file claims, and
          protect your livelihood from natural disasters.
        </p>
        <ul class="auth-features">
          <li><span class="icon">🌾</span> Coverage for flood, drought &amp; pests</li>
          <li><span class="icon">💰</span> Financial assistance up to ₱80,000</li>
          <li><span class="icon">📋</span> Simple online application</li>
          <li><span class="icon">⚡</span> Fast claim processing</li>
          <li><span class="icon">🏛️</span> Government-backed protection</li>
        </ul>
      </div>

      <!-- Right Panel -->
      <div class="auth-right" style="flex:1.5">
        <h2>Create Account</h2>
        <p class="auth-subtitle">Fill in your details to get started</p>

        <form id="signup-form" onsubmit="handleSignup(event)">
          <div class="form-row form-row-2">
            <div class="form-group">
              <label class="form-label">First Name</label>
              <input type="text" id="fname" class="form-control" placeholder="Juan" required />
            </div>
            <div class="form-group">
              <label class="form-label">Last Name</label>
              <input type="text" id="lname" class="form-control" placeholder="dela Cruz" required />
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" id="email" class="form-control" placeholder="juan@example.com" required />
          </div>

          <div class="form-row form-row-2">
            <div class="form-group">
              <label class="form-label">Phone Number</label>
              <input type="tel" id="phone" class="form-control" placeholder="09171234567"
                maxlength="13" oninput="formatPhoneInput(this)" required />
              <small id="phone-error" style="color:#dc3545;font-size:11.5px;display:none">
                Enter a valid PH mobile number (e.g. 09171234567 or +639171234567)
              </small>
            </div>
            <div class="form-group">
              <label class="form-label">Farmer Category</label>
              <select id="farmerType" class="form-select" required>
                <option value="">Select category</option>
                <option>Small Farmer</option>
                <option>Commercial Farmer</option>
                <option>Agrarian Reform Beneficiary</option>
                <option>Fisherfolk</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Home Address</label>
            <input type="text" id="address" class="form-control"
              placeholder="Brgy., Municipality, Province" required />
          </div>

          <div class="form-row form-row-2">
            <div class="form-group">
              <label class="form-label">Password</label>
              <div class="input-group">
                <span class="input-icon">🔒</span>
                <input type="password" id="password" class="form-control"
                  placeholder="Min. 8 characters" required
                  oninput="checkPasswordStrength(this.value)" />
                <span class="input-icon-right"
                  onclick="togglePassword('password', this)">👁️</span>
              </div>
              <!-- Password strength indicator -->
              <div id="pw-strength" style="margin-top:6px;font-size:12px;display:none">
                <div style="display:flex;gap:4px;margin-bottom:4px">
                  <div id="pw-bar-1" style="height:4px;flex:1;border-radius:2px;background:#ddd"></div>
                  <div id="pw-bar-2" style="height:4px;flex:1;border-radius:2px;background:#ddd"></div>
                  <div id="pw-bar-3" style="height:4px;flex:1;border-radius:2px;background:#ddd"></div>
                  <div id="pw-bar-4" style="height:4px;flex:1;border-radius:2px;background:#ddd"></div>
                </div>
                <div id="pw-rules" style="color:var(--text-muted,#666)">
                  <span id="rule-len"  style="margin-right:8px">❌ 8+ characters</span>
                  <span id="rule-up"   style="margin-right:8px">❌ Uppercase</span>
                  <span id="rule-num"  style="margin-right:8px">❌ Number</span>
                  <span id="rule-spec">❌ Special (!@#$)</span>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Confirm Password</label>
              <div class="input-group">
                <span class="input-icon">🔒</span>
                <input type="password" id="confirm-password" class="form-control"
                  placeholder="Repeat password" required />
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-check">
              <input type="checkbox" id="terms" required />
              <span style="font-size:13px;color:var(--text-secondary)">
                I agree to the
                <a class="auth-link" href="#">Terms &amp; Conditions</a> and
                <a class="auth-link" href="#">Privacy Policy</a>
              </span>
            </label>
          </div>

          <button type="submit" class="btn-primary-auth">Create Account →</button>
        </form>

        <div class="auth-divider">already have an account?</div>
        <div style="text-align:center">
          <a class="auth-link" onclick="navigateTo('../../index.php')">Sign in here</a>
        </div>
      </div>

    </div>
  </div>

  <?php require_once '../../includes/toast.php'; ?>
  <script>
    function formatPhoneInput(input) {
      // Strip all non-digit characters except leading +
      let v = input.value;
      // Allow only digits and + at the start
      v = v.replace(/[^0-9+]/g, '');
      // Ensure + only at position 0
      if (v.indexOf('+') > 0) v = v.replace(/\+/g, '');
      input.value = v;

      const err = document.getElementById('phone-error');
      const valid = /^(09\d{9}|\+639\d{9})$/.test(v);
      if (v.length > 0) {
        err.style.display = valid ? 'none' : 'block';
        input.style.borderColor = valid ? '' : '#dc3545';
      } else {
        err.style.display = 'none';
        input.style.borderColor = '';
      }
    }

    function isValidPHPhone(v) {
      return /^(09\d{9}|\+639\d{9})$/.test(v.trim());
    }

    function checkPasswordStrength(v) {
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
        el.style.color  = r.ok ? '#28a745' : '#dc3545';
        if (r.ok) score++;
      });
      const colors = ['#ddd', '#dc3545', '#fd7e14', '#ffc107', '#28a745'];
      for (let i = 1; i <= 4; i++) {
        document.getElementById('pw-bar-' + i).style.background =
          i <= score ? colors[score] : '#ddd';
      }
    }

    async function handleSignup(e) {
      e.preventDefault();
      const password = document.getElementById('password').value;
      const confirm  = document.getElementById('confirm-password').value;
      const phone    = document.getElementById('phone').value.trim();

      if (!isValidPHPhone(phone)) {
        showToast('Invalid Phone', 'Enter a valid PH mobile number (e.g. 09171234567).', 'error');
        document.getElementById('phone').focus();
        document.getElementById('phone-error').style.display = 'block';
        return;
      }

      if (password !== confirm) {
        showToast('Error', 'Passwords do not match!', 'error');
        return;
      }

      const btn = e.target.querySelector('button[type=submit]');
      btn.disabled = true;
      showLoading();

      try {
        const res = await api('POST', '/auth/register', {
          first_name: document.getElementById('fname').value.trim(),
          last_name:  document.getElementById('lname').value.trim(),
          email:      document.getElementById('email').value.trim(),
          password,
          phone:      document.getElementById('phone').value.trim(),
          address:    document.getElementById('address').value.trim(),
        }, false);

        hideLoading();
        if (res.success) {
          setSession(res.data.user, res.data.token);
          showToast('Account Created!', 'Welcome to the system. Redirecting...', 'success');
          setTimeout(() => { window.location.href = 'dashboard.php'; }, 1200);
        } else {
          const errs = res.errors
            ? Object.values(res.errors).flat().join(' ')
            : res.message;
          showToast('Registration Failed', errs || 'Please check your details.', 'error');
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
