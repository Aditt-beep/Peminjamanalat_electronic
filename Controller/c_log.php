
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../Model/m_log.php";

// Pastikan hanya admin yang bisa membuka log
if (
    !isset($_SESSION['data']) ||
    ($_SESSION['data']['role'] ?? '') !== 'admin'
) {
    header("Location: ../View/v_login.php");
    exit;
}

$logModel = new m_log();

// Ambil data dari database
$result = $logModel->tampil_log();

if ($result === false) {
    die("Gagal mengambil data log: " . mysqli_error((new koneksi())->koneksi));
}

$semuaLog = [];

while ($row = mysqli_fetch_assoc($result)) {
    $semuaLog[] = $row;
}

// Daftar aktivitas untuk pilihan filter
$daftar_aksi = [];

foreach ($semuaLog as $row) {
    $aksi = $row['aktivitas'] ?? '';

    if ($aksi !== '' && !in_array($aksi, $daftar_aksi, true)) {
        $daftar_aksi[] = $aksi;
    }
}

sort($daftar_aksi);

// Filter berdasarkan jenis aktivitas
$jenis = trim($_GET['jenis'] ?? '');

if ($jenis !== '') {
    $semuaLog = array_values(array_filter(
        $semuaLog,
        function ($row) use ($jenis) {
            return ($row['aktivitas'] ?? '') === $jenis;
        }
    ));
}

// Hitung pagination
$total = count($semuaLog);
$perPage = 20;
$totalPage = max(1, (int) ceil($total / $perPage));

$page = max(1, (int) ($_GET['page'] ?? 1));
$page = min($page, $totalPage);

$offset = ($page - 1) * $perPage;

// Data yang ditampilkan pada halaman aktif
$rows = array_slice($semuaLog, $offset, $perPage);

// Tampilkan view
require __DIR__ . "/../View/v_log.php";
?>
