<?php
$lines = file('resources/views/admin/database/database.blade.php');
foreach ($lines as $num => $line) {
    if (strpos($line, 'isChapterView') !== false || strpos($line, 'isAdminCSView') !== false || strpos($line, 'userRole') !== false) {
        if ($num < 800) {
            echo ($num + 1) . ': ' . trim($line) . "\n";
        }
    }
}
