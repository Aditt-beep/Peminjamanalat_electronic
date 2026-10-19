
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../Model/m_laporan.php";

if (
    !isset($_SESSION["data"]["id_user"]) ||
    !isset($_SESSION["data"]["role"])
) {
    header("Location: ../View/v_login.php");
    exit;
}

$role = strtolower(trim($_SESSION["data"]["role"]));

if (!in_array($role, ["admin", "petugas"], true)) {
    http_response_code(403);
    exit("Akses ditolak.");
}

$jenis = $_GET["jenis"] ?? "";

if (!in_array($jenis, ["", "peminjaman", "pengembalian"], true)) {
    $jenis = "";
}

$data_laporan = [];
$model = new m_laporan();

if ($jenis === "peminjaman") {
    $data_laporan = $model->laporan_peminjaman();
} elseif ($jenis === "pengembalian") {
    $data_laporan = $model->laporan_pengembalian();
}

require_once __DIR__ . "/../View/v_laporan.php";
?>
