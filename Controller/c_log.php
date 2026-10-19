<?php
/**
 * Controller/c_log.php
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Admin dan petugas boleh melihat log
if (
    !isset($_SESSION["data"]["id_user"]) ||
    !in_array($_SESSION["data"]["role"] ?? "", ["admin", "petugas"], true)
) {
    header("Location: ../View/v_login.php");
    exit;
}

require_once __DIR__ . "/../Model/m_log.php";

$model = new m_log();

$perPage = 20;
$page = max(1, (int)($_GET["page"] ?? 1));
$jenis = trim($_GET["jenis"] ?? "");   // bukan "aksi", karena itu dipakai routing controller

$total = $model->hitung($jenis);
$totalPage = max(1, (int)ceil($total / $perPage));
$page = min($page, $totalPage);

$rows = $model->daftar($jenis, $perPage, ($page - 1) * $perPage);
$daftar_aksi = $model->daftar_aksi();

require __DIR__ . "/../View/v_log_aktivitas.php";