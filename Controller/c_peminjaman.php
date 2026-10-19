
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../Model/m_peminjaman.php";

// Cek hak akses Admin.
if (
    !isset($_SESSION["data"]) ||
    ($_SESSION["data"]["role"] ?? "") !== "admin"
) {
    header("Location: ../View/v_login.php");
    exit;
}

class c_peminjaman
{
    private $model;

    public function __construct()
    {
        $this->model = new m_peminjaman();
    }

    public function simpan()
    {
        try {
            $id_user = (int) ($_POST["id_user"] ?? 0);
            $tanggal_pengajuan = $_POST["tanggal_pengajuan"] ?? "";
            $tanggal_pinjam = $_POST["tanggal_pinjam"] ?? "";
            $tanggal_kembali = $_POST["tanggal_kembali_rencana"] ?? "";
            $keperluan = trim($_POST["keperluan"] ?? "");
            $status = $_POST["status"] ?? "";

            $this->validasi(
                $id_user,
                $tanggal_pengajuan,
                $tanggal_pinjam,
                $tanggal_kembali,
                $keperluan,
                $status
            );

            $this->model->tambah(
                $id_user,
                $tanggal_pengajuan,
                $tanggal_pinjam,
                $tanggal_kembali,
                $keperluan,
                $status
            );

            header("Location: ../View/v_peminjaman.php");
            exit;
        } catch (Throwable $e) {
            $this->tampilkanError($e->getMessage());
        }
    }

    public function update()
    {
        try {
            $id_peminjaman = (int) ($_POST["id_peminjaman"] ?? 0);
            $id_user = (int) ($_POST["id_user"] ?? 0);
            $tanggal_pengajuan = $_POST["tanggal_pengajuan"] ?? "";
            $tanggal_pinjam = $_POST["tanggal_pinjam"] ?? "";
            $tanggal_kembali = $_POST["tanggal_kembali_rencana"] ?? "";
            $keperluan = trim($_POST["keperluan"] ?? "");
            $status = $_POST["status"] ?? "";

            if ($id_peminjaman < 1) {
                throw new Exception("ID peminjaman tidak valid.");
            }

            $this->validasi(
                $id_user,
                $tanggal_pengajuan,
                $tanggal_pinjam,
                $tanggal_kembali,
                $keperluan,
                $status
            );

            // Pastikan data yang akan diedit memang ada.
            if (!$this->model->get_by_id($id_peminjaman)) {
                throw new Exception("Data peminjaman tidak ditemukan.");
            }

            $this->model->update(
                $id_peminjaman,
                $id_user,
                $tanggal_pengajuan,
                $tanggal_pinjam,
                $tanggal_kembali,
                $keperluan,
                $status
            );

            header("Location: ../View/v_peminjaman.php");
            exit;
        } catch (Throwable $e) {
            error_log("Update peminjaman: " . $e->getMessage());
            $this->tampilkanError($e->getMessage());
        }
    }

    private function validasi(
        $id_user,
        $tanggal_pengajuan,
        $tanggal_pinjam,
        $tanggal_kembali,
        $keperluan,
        $status
    ) {
        $status_valid = [
            "menunggu",
            "disetujui",
            "ditolak",
            "selesai"
        ];

        if (
            $id_user < 1 ||
            $tanggal_pengajuan === "" ||
            $tanggal_pinjam === "" ||
            $tanggal_kembali === "" ||
            $keperluan === "" ||
            !in_array($status, $status_valid, true)
        ) {
            throw new Exception("Semua kolom wajib diisi dengan benar.");
        }

        foreach (
            [$tanggal_pengajuan, $tanggal_pinjam, $tanggal_kembali]
            as $tanggal
        ) {
            $date = DateTime::createFromFormat("!Y-m-d", $tanggal);

            if (!$date || $date->format("Y-m-d") !== $tanggal) {
                throw new Exception("Format tanggal tidak valid.");
            }
        }

        if ($tanggal_kembali < $tanggal_pinjam) {
            throw new Exception(
                "Tanggal kembali tidak boleh sebelum tanggal peminjaman."
            );
        }
    }

    private function tampilkanError($pesan)
    {
        http_response_code(400);

        echo "<!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1'>
            <title>Gagal Memproses Peminjaman</title>
        </head>
        <body style='font-family:Arial,sans-serif;padding:30px'>
            <h2>Data peminjaman gagal diproses</h2>
            <p>" .
                htmlspecialchars($pesan, ENT_QUOTES, "UTF-8") .
            "</p>
            <button onclick='history.back()'>Kembali ke Form</button>
        </body>
        </html>";

        exit;
    }

    public function hapus($id)
    {
        try {
            if ($id < 1) {
                throw new Exception("ID peminjaman tidak valid.");
            }

            if (!$this->model->get_by_id($id)) {
                throw new Exception("Data peminjaman tidak ditemukan.");
            }

            $this->model->hapus($id);

            header("Location: ../View/v_peminjaman.php");
            exit;
        } catch (Throwable $e) {
            error_log("Hapus peminjaman: " . $e->getMessage());
            $this->tampilkanError(
                "Data gagal dihapus. Pastikan data tidak masih digunakan oleh detail peminjaman."
            );
        }
    }
}

// Menjalankan aksi dari form atau URL.
$controller = new c_peminjaman();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["tambah"])) {
        $controller->simpan();
    } elseif (isset($_POST["edit"])) {
        $controller->update();
    } else {
        http_response_code(400);
        exit("Aksi tidak valid.");
    }
}

if (isset($_GET["hapus"])) {
    $controller->hapus((int) $_GET["hapus"]);
} elseif (
    isset($_GET["aksi"], $_GET["id"]) &&
    $_GET["aksi"] === "hapus"
) {
    $controller->hapus((int) $_GET["id"]);
}
?>
