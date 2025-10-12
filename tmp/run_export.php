<?php
chdir(__DIR__ . '/..');
$_GET['format'] = 'json';
ob_start();
include 'admin/export_report.php';
$out = ob_get_clean();
file_put_contents('/tmp/export_out.json', $out);
echo "Wrote /tmp/export_out.json\n";
