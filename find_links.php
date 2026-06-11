<?php
$lines = file('resources/views/admin/salesplan/index.blade.php');
foreach ($lines as $i => $line) {
    if (stripos($line, 'links(') !== false) {
        echo "Line " . ($i+1) . ": " . trim($line) . PHP_EOL;
    }
}
