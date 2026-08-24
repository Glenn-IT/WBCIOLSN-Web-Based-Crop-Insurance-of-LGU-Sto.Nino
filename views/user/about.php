<?php
$pageTitle   = 'About System & Developers — Crop Insurance';
$basePath    = '../../';
$currentPage = 'about';
$guardRole   = 'user';
require_once '../../includes/auth-guard.php';
require_once '../../includes/head.php';
?>
<body>
  <div class="app-layout">

    <?php require_once '../../includes/user-sidebar.php'; ?>

    <?php
    $topbarTitle    = 'About System';
    $topbarSubtitle = 'Learn more about the platform and the development team';
    $isAdmin        = false;
    require_once '../../includes/topbar.php';
    ?>

    <main class="main-content">
      <!-- Breadcrumbs & Title -->
      <div class="page-header">
        <div class="page-header-left">
          <div class="breadcrumb">
            <span>Home</span><span class="sep">›</span>
            <span class="current">About</span>
          </div>
          <h2>About the System & Development Team</h2>
        </div>
      </div>

      <!-- Hero Banner -->
      <div class="about-hero" style="
        background: linear-gradient(135deg, #1a6b3c 0%, #2d9c5a 55%, #f0a500 100%);
        border-radius: 16px;
        padding: 36px 32px;
        color: #ffffff;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(26, 107, 60, 0.2);
      ">
        <div style="max-width: 680px; position: relative; z-index: 2;">
          <span style="
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.2);
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 14px;
          ">
            🌾 Municipal Agriculture Office — LGU Sto. Niño
          </span>
          <h1 style="font-size: 26px; font-weight: 800; line-height: 1.3; margin-bottom: 12px;">
            Web-Based Crop Insurance Management System
          </h1>
          <p style="font-size: 14.5px; line-height: 1.6; opacity: 0.92; margin-bottom: 18px;">
            A digital crop insurance management platform designed to streamline insurance policy applications,
            expedite disaster damage claim verifications, and provide transparent tracking for local farmers in Sto. Niño.
          </p>
          <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <div style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); border-radius: 8px; padding: 8px 14px; font-size: 12.5px; font-weight: 600;">
              ⚡ Fast Claim Verification
            </div>
            <div style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); border-radius: 8px; padding: 8px 14px; font-size: 12.5px; font-weight: 600;">
              📱 PhilSMS & Email Alerts
            </div>
            <div style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); border-radius: 8px; padding: 8px 14px; font-size: 12.5px; font-weight: 600;">
              🛡️ Transparent & Secure
            </div>
          </div>
        </div>
        <div style="
          position: absolute;
          right: 20px;
          bottom: -20px;
          font-size: 160px;
          opacity: 0.15;
          user-select: none;
          pointer-events: none;
        ">
          🌾
        </div>
      </div>

      <!-- System Key Objectives Grid -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 36px;">
        <div class="card" style="padding: 24px;">
          <div style="font-size: 32px; margin-bottom: 12px;">🌱</div>
          <h3 style="font-size: 16px; font-weight: 700; color: #1a202c; margin-bottom: 8px;">Farmer Empowerment</h3>
          <p style="color: #718096; font-size: 13.5px; line-height: 1.6;">
            Providing farmers direct and easy access to apply for crop coverage anytime without paperwork hassles.
          </p>
        </div>
        <div class="card" style="padding: 24px;">
          <div style="font-size: 32px; margin-bottom: 12px;">📊</div>
          <h3 style="font-size: 16px; font-weight: 700; color: #1a202c; margin-bottom: 8px;">Real-Time Status Tracking</h3>
          <p style="color: #718096; font-size: 13.5px; line-height: 1.6;">
            Transparent timeline from application review to claim payout approval with SMS notifications.
          </p>
        </div>
        <div class="card" style="padding: 24px;">
          <div style="font-size: 32px; margin-bottom: 12px;">🛡️</div>
          <h3 style="font-size: 16px; font-weight: 700; color: #1a202c; margin-bottom: 8px;">Disaster Risk Mitigation</h3>
          <p style="color: #718096; font-size: 13.5px; line-height: 1.6;">
            Assisting the LGU in prompt indemnification response for crops affected by weather disasters or pests.
          </p>
        </div>
      </div>

      <!-- Section Divider / Header -->
      <div style="text-align: center; margin-bottom: 28px;">
        <div style="
          display: inline-block;
          background: #e8f5e9;
          color: #1a6b3c;
          font-weight: 700;
          font-size: 12px;
          padding: 4px 14px;
          border-radius: 20px;
          margin-bottom: 8px;
          text-transform: uppercase;
          letter-spacing: 0.5px;
        ">
          Engineering & Design
        </div>
        <h2 style="font-size: 24px; font-weight: 800; color: #1a202c;">Meet the Development Team</h2>
        <p style="color: #718096; font-size: 14px; max-width: 540px; margin: 6px auto 0;">
          The passionate developers behind the design, architecture, and implementation of this system.
        </p>
      </div>

      <!-- Developer Cards Grid (2 Developers) -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-bottom: 36px;">

        <!-- ================================================================= -->
        <!-- DEVELOPER 1 CARD -->
        <!-- Note: You can replace the image path in src and edit text below   -->
        <!-- ================================================================= -->
        <div class="card dev-card" style="overflow: hidden; border: 1px solid #e2e8f0; transition: transform 0.2s ease, box-shadow 0.2s ease;">
          <div style="
            background: linear-gradient(135deg, #1a6b3c 0%, #2d9c5a 100%);
            height: 100px;
            position: relative;
          ">
            <div style="
              position: absolute;
              bottom: -45px;
              left: 24px;
              width: 90px;
              height: 90px;
              border-radius: 50%;
              border: 4px solid #ffffff;
              box-shadow: 0 4px 12px rgba(0,0,0,0.15);
              overflow: hidden;
              background: #f0fdf4;
              display: flex;
              align-items: center;
              justify-content: center;
            ">
              <!-- Developer 1 Image -->
              <img
                src="../../assets/images/dev1.jpg"
                alt="Developer 1 Photo"
                id="dev1-img"
                style="width: 100%; height: 100%; object-fit: cover;"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
              />
              <!-- Fallback Avatar if image not yet placed -->
              <div style="
                display: none;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, #1a6b3c, #2d9c5a);
                color: #ffffff;
                font-size: 32px;
                font-weight: 700;
                align-items: center;
                justify-content: center;
              ">
                D1
              </div>
            </div>
          </div>

          <div style="padding: 56px 24px 24px 24px;">
            <!-- Developer 1 Details Placeholder -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
              <div>
                <h3 style="font-size: 19px; font-weight: 700; color: #1a202c; margin-bottom: 2px;">
                  Developer 1 Name
                </h3>
                <span style="color: #1a6b3c; font-weight: 600; font-size: 13px;">
                  Lead System Developer / Full-Stack
                </span>
              </div>
              <span style="
                background: #e8f5e9;
                color: #1a6b3c;
                font-size: 11px;
                font-weight: 700;
                padding: 4px 10px;
                border-radius: 12px;
              ">
                Developer #1
              </span>
            </div>

            <p style="color: #718096; font-size: 13px; line-height: 1.6; margin-bottom: 16px;">
              <!-- Replace this bio with your own details -->
              Responsible for system architecture, backend API development, database design,
              and integrating SMS & email notification gateways.
            </p>

            <div style="border-top: 1px solid #edf2f7; padding-top: 14px; display: flex; flex-direction: column; gap: 8px;">
              <div style="display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: #4a5568;">
                <span>🎓</span>
                <span>BS in Information Technology</span>
              </div>
              <div style="display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: #4a5568;">
                <span>📧</span>
                <span>developer1@example.com</span>
              </div>
              <div style="display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: #4a5568;">
                <span>💻</span>
                <span>Core Backend, Auth & Architecture</span>
              </div>
            </div>
          </div>
        </div>

        <!-- ================================================================= -->
        <!-- DEVELOPER 2 CARD -->
        <!-- Note: You can replace the image path in src and edit text below   -->
        <!-- ================================================================= -->
        <div class="card dev-card" style="overflow: hidden; border: 1px solid #e2e8f0; transition: transform 0.2s ease, box-shadow 0.2s ease;">
          <div style="
            background: linear-gradient(135deg, #1565c0 0%, #1976d2 100%);
            height: 100px;
            position: relative;
          ">
            <div style="
              position: absolute;
              bottom: -45px;
              left: 24px;
              width: 90px;
              height: 90px;
              border-radius: 50%;
              border: 4px solid #ffffff;
              box-shadow: 0 4px 12px rgba(0,0,0,0.15);
              overflow: hidden;
              background: #eff6ff;
              display: flex;
              align-items: center;
              justify-content: center;
            ">
              <!-- Developer 2 Image -->
              <img
                src="../../assets/images/dev2.jpg"
                alt="Developer 2 Photo"
                id="dev2-img"
                style="width: 100%; height: 100%; object-fit: cover;"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
              />
              <!-- Fallback Avatar if image not yet placed -->
              <div style="
                display: none;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, #1565c0, #1976d2);
                color: #ffffff;
                font-size: 32px;
                font-weight: 700;
                align-items: center;
                justify-content: center;
              ">
                D2
              </div>
            </div>
          </div>

          <div style="padding: 56px 24px 24px 24px;">
            <!-- Developer 2 Details Placeholder -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
              <div>
                <h3 style="font-size: 19px; font-weight: 700; color: #1a202c; margin-bottom: 2px;">
                  Developer 2 Name
                </h3>
                <span style="color: #1565c0; font-weight: 600; font-size: 13px;">
                  Frontend & UI/UX Developer
                </span>
              </div>
              <span style="
                background: #e3f2fd;
                color: #1565c0;
                font-size: 11px;
                font-weight: 700;
                padding: 4px 10px;
                border-radius: 12px;
              ">
                Developer #2
              </span>
            </div>

            <p style="color: #718096; font-size: 13px; line-height: 1.6; margin-bottom: 16px;">
              <!-- Replace this bio with your own details -->
              Responsible for user experience design, responsive client interfaces, farmer portal workflow,
              and interactive claims and reports dashboards.
            </p>

            <div style="border-top: 1px solid #edf2f7; padding-top: 14px; display: flex; flex-direction: column; gap: 8px;">
              <div style="display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: #4a5568;">
                <span>🎓</span>
                <span>BS in Information Technology</span>
              </div>
              <div style="display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: #4a5568;">
                <span>📧</span>
                <span>developer2@example.com</span>
              </div>
              <div style="display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: #4a5568;">
                <span>🎨</span>
                <span>Frontend, UI/UX & Responsive Layouts</span>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- Quick Edit Instructions Card for the user -->
      <div class="card" style="padding: 20px 24px; background: #fff8e1; border: 1px solid #ffe082; border-radius: 12px; margin-bottom: 28px;">
        <div style="display: flex; gap: 14px; align-items: flex-start;">
          <span style="font-size: 26px;">💡</span>
          <div>
            <h4 style="color: #b78103; font-size: 14.5px; font-weight: 700; margin-bottom: 4px;">
              How to add your developer photos and details:
            </h4>
            <ul style="color: #7a5800; font-size: 13px; margin-left: 18px; line-height: 1.6;">
              <li><strong>Photos:</strong> Place your photos inside <code>assets/images/dev1.jpg</code> and <code>assets/images/dev2.jpg</code> (PNG and JPG both work).</li>
              <li><strong>Details:</strong> Edit <code>views/user/about.php</code> to customize your names, roles, course, bios, and email addresses anytime.</li>
            </ul>
          </div>
        </div>
      </div>

    </main>
  </div>

  <?php require_once '../../includes/toast.php'; ?>
  <script src="../../assets/js/app.js"></script>
</body>
</html>
