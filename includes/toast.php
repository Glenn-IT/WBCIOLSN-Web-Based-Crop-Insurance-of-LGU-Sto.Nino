<?php
// $basePath must already be set by the calling page (same value used in head.php).
// This file is included at the bottom of <body>, before the page-specific <script> block.
$basePath = $basePath ?? '../../';
?>
<div id="toast-container"></div>
<script src="<?= $basePath ?>assets/js/app.js"></script>
