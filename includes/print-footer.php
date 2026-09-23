<?php
/**
 * Shared Government Footer for Reports and Print Outputs
 * Web-Based Crop Insurance System — LGU Sto. Niño, Cagayan
 * 
 * Variables that can optionally be pre-set by the parent page:
 *   $basePath        (string) - Relative path to root, e.g. '../../' (default: '../../')
 *   $govDocType      (string) - Document title or type (default: 'Official Report')
 *   $govDocRef       (string) - Document reference code (optional)
 *   $govIncludeSeal  (bool)   - Whether to display the small official emblem (default: true)
 */
$basePath       = $basePath ?? '../../';
$govDocType     = $govDocType ?? 'Official Report';
$govIncludeSeal = $govIncludeSeal ?? true;
$govDocRef      = $govDocRef ?? ('WBCI-DOC-' . date('Ymd-His'));
?>
<div class="official-gov-footer" id="official-gov-footer">
  <div class="gov-footer-divider"></div>
  
  <div class="gov-footer-body">
    <div class="gov-footer-brand">
      <?php if ($govIncludeSeal): ?>
      <img src="<?= htmlspecialchars($basePath) ?>img/Agri-Sto-Logo.png" alt="MAO Sto. Niño Seal" class="gov-footer-seal" onerror="this.style.display='none'" />
      <?php endif; ?>
      <div class="gov-footer-brand-text">
        <div class="gov-office-title">MUNICIPAL AGRICULTURE OFFICE</div>
        <div class="gov-office-sub">Local Government Unit of Sto. Niño • Province of Cagayan</div>
        <div class="gov-office-addr">📍 Municipal Hall Compound, Centro Norte, Sto. Niño (Faire), Cagayan 3525</div>
      </div>
    </div>

    <div class="gov-footer-info-grid">
      <div class="gov-info-col">
        <div class="gov-info-heading">Official Contact</div>
        <div class="gov-info-row"><span>Email:</span> <a href="mailto:agriculture@stonino-cagayan.gov.ph" style="color:inherit;text-decoration:none">agriculture@stonino-cagayan.gov.ph</a></div>
        <div class="gov-info-row"><span>Hotline:</span> (078) 377-2001 / +63 917 123 4567</div>
        <div class="gov-info-row"><span>Portal:</span> www.stonino-cagayan.gov.ph</div>
      </div>

      <div class="gov-info-col gov-doc-ctrl">
        <div class="gov-info-heading">Document Control & Security</div>
        <div class="gov-info-row"><span>System:</span> WBCI-OLSN v1.0</div>
        <div class="gov-info-row"><span>Document:</span> <?= htmlspecialchars($govDocType) ?></div>
        <div class="gov-info-row"><span>Doc Ref:</span> <strong class="gov-doc-ref-text"><?= htmlspecialchars($govDocRef) ?></strong></div>
      </div>
    </div>
  </div>

  <div class="gov-footer-notice">
    <div class="gov-notice-text">
      ⚖️ <em>Official Computer-Generated Document — Local Government Unit of Sto. Niño, Cagayan. Valid only with authorized signature(s) and official municipal dry seal. Any unauthorized alteration or reproduction is punishable by law.</em>
    </div>
    <div class="gov-notice-motto">
      <span>★ Serbisyong Tapat at Dekalidad Para sa Magsasakang Sto. Niñeno ★</span>
      <span class="gov-republic-text">Republic of the Philippines</span>
    </div>
  </div>
</div>
