<?php
chdir(__DIR__ . '/..');

if (php_sapi_name() !== 'cli') die('Use CLI');
$_GET['format'] = 'json';
ob_start();
include 'admin/export_report.php';
$out = ob_get_clean();
file_put_contents('/tmp/export_report_cli.json', $out);
echo "Wrote /tmp/export_report_cli.json\n";
