
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../Model/m_peminjaman.php";
require_once __DIR__ . "/../Model/m_alat.php";

// Pastikan pengguna sudah login.
if (!isset($_SESSION["data"]["id_user"])) {
    header("Location: ../View/v_login.php");
    exit;
}

$user = $_SESSION["data"];
$role = strtolower(trim($user["role"] ?? ""));
$id_user = (int)$user["id_user"];

$aksi = $_GET["aksi"] ?? "riwayat";

$model = new m_peminjaman();

function kembaliDenganPesan($pesan, $tujuan)
{
    $_SESSION["pesan_pengajuan"] = $pesan;
    header("Location: " . $tujuan);
    exit;
}

// =====================================
// PEMINJAM: MENAMPILKAN FORM PENGAJUAN
// =====================================
if ($aksi === "form") {
    if ($role !== "peminjam") {
        http_response_code(403);
        exit("Role akun lo bukan peminjam. Role terbaca: " .
            htmlspecialchars($role));
    }

    $id_alat = filter_input(INPUT_GET, "id_alat", FILTER_VALIDATE_INT);

    if (!$id_alat) {
        exit("ID alat tidak valid.");
    }

    $alatModel = new m_alat();
    $alat = $alatModel->get_by_id($id_alat);

    if (
        !$alat ||
        ($alat["status"] ?? "") !== "tersedia" ||
        (int)($alat["jumlah"] ?? 0) < 1
    ) {
        exit("Alat tidak tersedia untuk diajukan.");
    }

    include __DIR__ . "/../View/v_form_pengajuan.php";
    exit;
}

// =====================================
// PEMINJAM: MENYIMPAN PENGAJUAN
// =====================================
if ($aksi === "simpan" && $_SERVER["REQUEST_METHOD"] === "POST") {
    if ($role !== "peminjam") {
        http_response_code(403);
        exit("Role akun lo bukan peminjam. Role terbaca: " .
            htmlspecialchars($role));
    }

    $id_alat = filter_input(INPUT_POST, "id_alat", FILTER_VALIDATE_INT);
    $jumlah = filter_input(INPUT_POST, "jumlah", FILTER_VALIDATE_INT);

    $tanggal_pinjam = trim($_POST["tanggal_pinjam"] ?? "");
    $tanggal_kembali = trim($_POST["tanggal_kembali_rencana"] ?? "");
    $keperluan = trim($_POST["keperluan"] ?? "");

    if (
        !$id_alat ||
        !$jumlah ||
        $tanggal_pinjam === "" ||
        $tanggal_kembali === "" ||
        $keperluan === ""
    ) {
        kembaliDenganPesan(
            "Semua data pengajuan wajib diisi.",
            "../Controller/c_pengajuan.php?aksi=riwayat"
        );
    }

    $hasil = $model->ajukan(
        $id_user,
        (int)$id_alat,
        (int)$jumlah,
        $tanggal_pinjam,
        $tanggal_kembali,
        $keperluan
    );

    kembaliDenganPesan(
        $hasil["pesan"],
        "../Controller/c_pengajuan.php?aksi=riwayat"
    );
}

// =====================================
// PETUGAS: DAFTAR PENGAJUAN
// =====================================
if ($aksi === "petugas") {
    if ($role !== "petugas") {
        http_response_code(403);
        exit("Akses hanya untuk petugas.");
    }

    $data_pengajuan = $model->daftar_pengajuan();

    include __DIR__ . "/../View/v_pengajuan_petugas.php";
    exit;
}

// =====================================
// PETUGAS: MENYETUJUI ATAU MENOLAK
// =====================================
if ($aksi === "proses" && $_SERVER["REQUEST_METHOD"] === "POST") {
    if ($role !== "petugas") {
        http_response_code(403);
        exit("Akses hanya untuk petugas.");
    }

    $id_peminjaman = filter_input(
        INPUT_POST,
        "id_peminjaman",
        FILTER_VALIDATE_INT
    );

    $pilihan = $_POST["pilihan"] ?? "";
    $keterangan = trim($_POST["keterangan"] ?? "");

    if (
        !$id_peminjaman ||
        !in_array($pilihan, ["setujui", "tolak"], true)
    ) {
        kembaliDenganPesan(
            "Data pengajuan tidak valid.",
            "../Controller/c_pengajuan.php?aksi=petugas"
        );
    }

    $hasil = $model->proses_pengajuan(
        (int)$id_peminjaman,
        $id_user,
        $pilihan,
        $keterangan
    );

    kembaliDenganPesan(
        $hasil["pesan"],
        "../Controller/c_pengajuan.php?aksi=petugas"
    );
}

// =====================================
// PEMINJAM: RIWAYAT PENGAJUAN
// =====================================
if ($aksi === "riwayat") {
    if ($role !== "peminjam") {
        http_response_code(403);
        exit("Akses riwayat hanya untuk peminjam.");
    }

    $data_pengajuan = $model->pengajuan_saya($id_user);

    include __DIR__ . "/../View/v_riwayat_pengajuan.php";
    exit;
}

// =====================================
// AKSI TIDAK DIKENALI
// =====================================
http_response_code(404);
echo "Aksi tidak ditemukan.";
?>
