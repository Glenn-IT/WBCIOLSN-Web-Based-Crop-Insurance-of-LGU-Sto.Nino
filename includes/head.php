<?php
// Variables the calling page may set before including this file:
//   $pageTitle   (string)  — browser tab title, e.g. "Dashboard — Farmer Portal"
//   $basePath    (string)  — relative path back to project root, e.g. "../../"
//   $includeChartJs (bool) — set true on pages that use Chart.js charts
$pageTitle      = $pageTitle      ?? 'LGU Sto. Niño Crop Insurance';
$basePath       = $basePath       ?? '../../';
$includeChartJs = $includeChartJs ?? false;
$includeLeaflet = $includeLeaflet ?? false;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌽</text></svg>" />
  <link rel="stylesheet" href="<?= $basePath ?>assets/css/style.css" />
  <?php if ($includeChartJs): ?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <?php endif; ?>
  <?php if ($includeLeaflet): ?>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <?php endif; ?>
</head>
