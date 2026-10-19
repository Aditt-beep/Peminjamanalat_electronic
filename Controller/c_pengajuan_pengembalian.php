
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../Model/m_pengajuan_pengembalian.php";

// Periksa login
if (
    !isset($_SESSION["data"]["id_user"]) ||
    !isset($_SESSION["data"]["role"])
) {
    header("Location: ../View/v_login.php");
    exit;
}

$id_user = (int) $_SESSION["data"]["id_user"];
$role = strtolower(trim($_SESSION["data"]["role"]));
$aksi = $_GET["aksi"] ?? "form";

$model = new m_pengajuan_pengembalian();

// ================================
// FORM PENGEMBALIAN UNTUK PEMINJAM
// ================================
if ($aksi === "form") {
    if ($role !== "peminjam") {
        http_response_code(403);
        exit("Halaman ini hanya untuk peminjam.");
    }

    $data_detail = $model->detail_peminjam($id_user);

    require_once __DIR__ . "/../View/v_form_pengajuan_pengembalian.php";
    exit;
}

// ================================
// SIMPAN PENGAJUAN DARI PEMINJAM
// ================================
if ($aksi === "simpan") {
    if (
        $role !== "peminjam" ||
        $_SERVER["REQUEST_METHOD"] !== "POST"
    ) {
        http_response_code(403);
        exit("Akses ditolak.");
    }

    $id_detail = (int) ($_POST["id_detail"] ?? 0);
    $keterangan = trim($_POST["keterangan"] ?? "");

    if ($id_detail <= 0) {
        $_SESSION["pesan_pengajuan_kembali"] =
            "Silakan pilih alat yang ingin dikembalikan.";
    } else {
        $hasil = $model->ajukan(
            $id_user,
            $id_detail,
            $keterangan
        );

        $_SESSION["pesan_pengajuan_kembali"] = $hasil["pesan"];
    }

    header(
        "Location: ../View/v_homepeminjam.php"
    );
    exit;
}

// ================================
// DAFTAR PENGAJUAN UNTUK PETUGAS
// ================================
if ($aksi === "petugas") {
    if ($role !== "petugas") {
        http_response_code(403);
        exit("Halaman ini hanya untuk petugas.");
    }

    $data_pengajuan = $model->daftar_petugas();

    require_once __DIR__ . "/../View/v_pengajuan_pengembalian_petugas.php";
    exit;
}

// ================================
// KONFIRMASI PENGEMBALIAN PETUGAS
// ================================
if ($aksi === "konfirmasi") {
    if (
        $role !== "petugas" ||
        $_SERVER["REQUEST_METHOD"] !== "POST"
    ) {
        http_response_code(403);
        exit("Akses ditolak.");
    }

    $id_pengajuan = (int) (
        $_POST["id_pengajuan_kembali"] ?? 0
    );

    $kondisi = trim(
        $_POST["kondisi_kembali"] ?? "baik"
    );

    $keterangan = trim(
        $_POST["keterangan"] ?? ""
    );

    $kondisi_valid = [
        "baik",
        "rusak_ringan",
        "rusak_berat"
    ];

    if (
        $id_pengajuan <= 0 ||
        !in_array($kondisi, $kondisi_valid, true)
    ) {
        $_SESSION["pesan_pengajuan_kembali"] =
            "Data pengembalian tidak valid.";
    } else {
        $hasil = $model->konfirmasi(
            $id_pengajuan,
            $kondisi,
            $keterangan
        );

        $_SESSION["pesan_pengajuan_kembali"] =
            $hasil["pesan"];
    }

    header(
        "Location: c_pengajuan_pengembalian.php?aksi=petugas"
    );
    exit;
}

// ================================
// AKSI TIDAK DIKENALI
// ================================
http_response_code(404);
echo "Aksi pengembalian tidak ditemukan.";

?>
