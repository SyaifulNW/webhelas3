<?php
$file = 'c:\\xampp\\htdocs\\webhelas\\resources\\views\\admin\\keuangan\\kas_kecil.blade.php';
$content = file_get_contents($file);

$search = <<<HTML
                <a href="{{ route('admin.keuangan.kas-kecil.export-pdf', ['bulan' => \$selectedMonth, 'tahun' => \$selectedYear, 'kategori' => \$kategori]) }}"
                    class="btn btn-danger btn-sm ml-2 shadow-sm">
                    <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                </a>
HTML;

$search_alt = "                <a href=\"{{ route('admin.keuangan.kas-kecil.export-pdf', ['bulan' => \$selectedMonth, 'tahun' => \$selectedYear, 'kategori' => \$kategori]) }}\"\r\n                    class=\"btn btn-danger btn-sm ml-2 shadow-sm\">\r\n                    <i class=\"fas fa-file-pdf mr-1\"></i> Cetak PDF\r\n                </a>";

$replace = <<<HTML
                <button type="submit" class="btn btn-danger btn-sm ml-2 shadow-sm" formaction="{{ route('admin.keuangan.kas-kecil.export-pdf') }}">
                    <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                </button>
HTML;

if (strpos($content, $search) !== false) {
    $content = str_replace($search, $replace, $content);
    file_put_contents($file, $content);
    echo "Replaced search 1";
} else if (strpos($content, $search_alt) !== false) {
    $content = str_replace($search_alt, $replace, $content);
    file_put_contents($file, $content);
    echo "Replaced search alt";
} else {
    // Try regex
    $pattern = '/<a href="\{\{ route\(\'admin.keuangan.kas-kecil.export-pdf\'.*?<\/a>/s';
    $content = preg_replace($pattern, $replace, $content);
    file_put_contents($file, $content);
    echo "Regex replaced";
}
