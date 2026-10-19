
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../Model/m_pengembalian.php";

class c_pengembalian
{
    private $model;

    public function __construct()
    {
        $this->model = new m_pengembalian();
    }

    // Memastikan pengguna yang mengakses adalah Petugas
    private function cek_petugas()
    {
        if (
            !isset($_SESSION["data"]["id_user"]) ||
            ($_SESSION["data"]["role"] ?? "") !== "petugas"
        ) {
            header("Location: ../View/v_login.php");
            exit;
        }
    }

    // Menampilkan form pengembalian
    public function tambah()
    {
        $this->cek_petugas();

        $data_detail = $this->model->daftar_detail_belum_kembali();

        include __DIR__ . "/../View/v_pengajuan_pengembalian_petugas.php";
    }

    // Menghitung denda melalui Model untuk ditampilkan di View
    public function hitung_denda_ajax()
    {
        $this->cek_petugas();

        header("Content-Type: application/json; charset=utf-8");

        $id_detail = (int) ($_GET["id_detail"] ?? 0);
        $tanggal_kembali = $_GET["tanggal_kembali"] ?? "";

        if (
            $id_detail <= 0 ||
            !is_string($tanggal_kembali) ||
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_kembali)
        ) {
            http_response_code(400);
            echo json_encode([
                "error" => "Input alat atau tanggal tidak valid."
            ]);
            exit;
        }

        $tanggalInput = DateTime::createFromFormat(
            "!Y-m-d",
            $tanggal_kembali
        );

        if (
            !$tanggalInput ||
            $tanggalInput->format("Y-m-d") !== $tanggal_kembali ||
            $tanggal_kembali > date("Y-m-d")
        ) {
            http_response_code(400);
            echo json_encode([
                "error" => "Tanggal pengembalian tidak valid."
            ]);
            exit;
        }

        $tanggal_rencana = $this->model->get_tanggal_jatuh_tempo(
            $id_detail
        );

        if (!$tanggal_rencana) {
            http_response_code(404);
            echo json_encode([
                "error" => "Data peminjaman tidak ditemukan atau sudah dikembalikan."
            ]);
            exit;
        }

        $hasil = $this->model->hitung_denda(
            $tanggal_rencana,
            $tanggal_kembali
        );

        if ($hasil === false) {
            http_response_code(400);
            echo json_encode([
                "error" => "Perhitungan denda gagal."
            ]);
            exit;
        }

        echo json_encode([
            "tanggal_jatuh_tempo" => $tanggal_rencana,
            "hari_terlambat" => $hasil["hari_terlambat"],
            "total_denda" => $hasil["total_denda"]
        ]);

        exit;
    }

    // Menyimpan pengembalian ke database
    public function simpan()
    {
        $this->cek_petugas();

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: c_pengembalian.php?aksi=tambah");
            exit;
        }

        $id_detail = (int) ($_POST["id_detail"] ?? 0);
        $tanggal_kembali = trim($_POST["tanggal_kembali"] ?? "");
        $kondisi_kembali = trim($_POST["kondisi_kembali"] ?? "");
        $keterangan = trim($_POST["keterangan"] ?? "");
        $status_bayar = $_POST["status_bayar"] ?? "belum_bayar";

        $tanggalInput = DateTime::createFromFormat(
            "!Y-m-d",
            $tanggal_kembali
        );

        if (
            $id_detail <= 0 ||
            !$tanggalInput ||
            $tanggalInput->format("Y-m-d") !== $tanggal_kembali ||
            $tanggal_kembali > date("Y-m-d") ||
            !in_array(
                $kondisi_kembali,
                ["Baik", "Rusak", "Hilang"],
                true
            ) ||
            !in_array(
                $status_bayar,
                ["belum_bayar", "lunas"],
                true
            )
        ) {
            $_SESSION["pesan_pengembalian"] = [
                "jenis" => "error",
                "isi" => "Data pengembalian tidak valid. Periksa kembali form."
            ];

            header("Location: c_pengembalian.php?aksi=tambah");
            exit;
        }

        $berhasil = $this->model->tambah(
            $id_detail,
            $tanggal_kembali,
            $kondisi_kembali,
            $keterangan,
            $status_bayar
        );

        if ($berhasil) {
            $_SESSION["pesan_pengembalian"] = [
                "jenis" => "success",
                "isi" => "Data pengembalian berhasil disimpan."
            ];
        } else {
            $_SESSION["pesan_pengembalian"] = [
                "jenis" => "error",
                "isi" => "Pengembalian gagal disimpan. Periksa data dan status peminjaman."
            ];
        }

        header("Location: c_pengembalian.php?aksi=tambah");
        exit;
    }
}

// Routing berdasarkan parameter aksi
if (isset($_GET["aksi"])) {
    $controller = new c_pengembalian();

    switch ($_GET["aksi"]) {
        case "tambah":
            $controller->tambah();
            break;

        case "hitung_denda":
            $controller->hitung_denda_ajax();
            break;

        case "simpan":
            $controller->simpan();
            break;

        default:
            header("Location: ../View/v_home_petugas.php");
            exit;
    }
} else {
    header("Location: ../View/v_home_petugas.php");
    exit;
}
?>
